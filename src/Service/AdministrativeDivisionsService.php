<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use App\Entity\GeonamesCountry;
use App\Adapter\GeonamesAdapter;
use App\Entity\GeonamesCountryLevel;
use App\Entity\GeonamesCountryLocale;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AdministrativeDivisionLocale;
use App\Entity\GeonamesAdministrativeDivision;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdministrativeDivisionsService
{
    public function __construct(
        #[Autowire('%env(ALT_CODES_FETCH)%')]
        private readonly bool $altCodes,
        private GeonamesAPIServiceInterface $apiservice,
        private EntityManagerInterface $entityManager,
        private CacheItemPoolInterface $redisCache,
        private GeonamesTranslationService $translationService,
        private GeonamesDBCachingService $dbservice,
        private string $redisDsn,
        private LoggerInterface $logger,
    ) {
    }
    public function purgeAdminDivisions(string $fcode): string
    {
        if ($this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->removeByFcode($fcode)) {
            $this->entityManager->flush();
            return "Success";
        }

        return "Error";
    }

    public function getCountriesLevel(string $fcode): JsonResponse
    {
        $response = new JsonResponse();
        $countryLevelRepository = $this->entityManager->getRepository(GeonamesCountryLevel::class);

        $countryLevels = $countryLevelRepository->findUsedLevelMoreThan(substr($fcode, -1));

        foreach ($countryLevels as $countryLevel) {
            $countriesToUpdate[] = $countryLevel->getCountryCode();
        }

        if (isset($countriesToUpdate)) {
            $response->setContent(json_encode($countriesToUpdate));
        }
        return $response;
    }

    public function addAdminDivisionsBatch(string $fcode, int $startrow, array|string $countries): JsonResponse
    {
        $response = new JsonResponse();
        $count = 0;
        if (is_array($countries)) {
            foreach ($countries as $countryKey => $country) {
                self::addAdminDivisions($fcode, $startrow, $country);
                $count++;
            }
        } else return self::addAdminDivisions($fcode, $startrow, $countries);

        $response->setContent('Countries done : ' . $count);
        return $response;
    }

    public function addAdminDivisions(string $fcode, int $startrow, array|string $countries): JsonResponse
    {
        $response = new JsonResponse();
        $newEntryCount = 0;
        $entriesFoundCount = 0;
        $adminDivRepository = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class);
        try {
            $apiResult = json_decode($this->apiservice->searchJSON($fcode, $startrow, $countries)->getContent());
        } catch (\Exception $e) {
            throw new BadRequestException('Error during Geonames searchJSON request.');
        }

        if (count($apiResult->geonames) != 0) {
            $trouve = [];
            $pastrouve = [];
            foreach ($apiResult->geonames as $entry) {
                if (!$adminDivRepository->findOneByGeonameId($entry->geonameId)) {
                    $this->dbservice->saveSubdivisionToDatabase($entry);
                    $newEntryCount++;
                    $pastrouve[] = $entry->name;
                } else {
                    $entriesFoundCount++;
                    $trouve[] = $entry->name;
                }
            }
            $this->entityManager->flush();
        }
        //ATTENTION le tableau retourné n'est pas vraiment cohérent,
        //certaines fois il manque des éléments...
        $result = ['Status' => 'Success', 'Entries already found' => $entriesFoundCount, 'New entries' => $newEntryCount, 'Max entries for this fcode' => $apiResult->totalResultsCount];

        $response->setContent(json_encode($result));

        return $response;
    }

    public function addChildrenDivisions(array $geonameParents): string
    {
        set_time_limit(0);
        $outputCount = 0;
        foreach ($geonameParents as $geonameParent) {
            $geonameCountry = $this->entityManager->getRepository(GeonamesCountry::class)->findOneByCountryCode($geonameParent);
            $countryUsedLevel = $geonameCountry->getLevel()->getUsedLevel();
            $this->logger->info(
                '👉 Starting crawl for country code ' .
                    $geonameParent .
                    ' 👈'
            );
            $finalChildren = $this->getChildrenDivs($geonameCountry->getGeonameId(), $countryUsedLevel, 0);
            $this->dbservice->saveChildren($finalChildren);
            $outputCount += count($finalChildren);
            #Sometimes the geonames API is in the cabbages, a small delay in the next requests can help
            sleep(1);
        }

        return json_encode(['Status' => 'Success', 'New children subdivisions' => $outputCount]);
    }

    private function getChildrenDivs(
        int $parentId,
        int $countryUsedLevel,
        int $currentDepth,
        array $childrens = []
    ): array {
        //usage principal : codes ADM1-2-3
        $forbiddenFCodes = ['PPL', 'PPLL', 'PPLA', 'PPLA2', 'PPLC',];
        $adminDivRepository = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class);

        if ($currentDepth >= $countryUsedLevel) {

            return $childrens;
        }
        try {
            $childrenDivs = $this->apiservice->childrenJSON($parentId);
        } catch (\Exception $e) {
            throw new BadRequestException('Error during Geonames searchJSON request : ' . $e->getMessage());
        }
        //Specific case for American Samoa id 5880801
        if (array_key_exists('status', $childrenDivs)) {
            $this->logger->warning(
                '  ❌ ' .
                    $childrenDivs['status']['message']
            );
            return $childrens;
        }
        if (!array_key_exists('geonames', $childrenDivs) || !count($childrenDivs['geonames'])) {

            return $childrens;
        }

        foreach ($childrenDivs['geonames'] as $childDiv) {
            if (
                $adminDivRepository->findOneByGeonameId($childDiv['geonameId'])
                || in_array($childDiv['fcode'], $forbiddenFCodes)
            ) {
                $this->logger->info(
                    '⏹️  Skipping ' .
                        $childDiv['geonameId'] . '(' .
                        $childDiv['name'] . ')' . '...'
                );
                continue;
            }

            $childrens[] = GeonamesAdapter::AdaptObjToSubdiv((object)$childDiv);

            $this->logger->info('✅ Adding GeonameId ' . $childDiv['geonameId'] . '(' . $childDiv['name'] . ')' . ' to the repository');

            $childrens =
                $this->getChildrenDivs(
                    $childDiv['geonameId'],
                    $countryUsedLevel,
                    $currentDepth + 1,
                    $childrens
                );
        }
        return $childrens;
    }

    public function updateAlternativeCodes(): JsonResponse
    {
        $response = new JsonResponse();
        $count = 0;
        $notFound = 0;
        $idsNotFound = [];
        if ($altCodesList = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_alternative_divisions.json'))) {
            foreach ($altCodesList as $altCode) {
                if ($adminDiv = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId($altCode->geonameId)) {

                    $adminDiv->setAdminCodeAlt1($altCode->adminCodes1 ?? null)
                        ->setAdminCodeAlt2($altCode->adminCodes2 ?? null)
                        ->setAdminCodeAlt3($altCode->adminCodes3 ?? null);
                    $this->entityManager->persist($adminDiv);
                    $count++;
                } else {
                    $notFound++;
                    $idsNotFound[] = $altCode->geonameId;
                }
            }
            $this->entityManager->flush();
            $content = $count . ' alternative codes inserted. ' . $notFound . ' Ids not found in base : ' . (implode(",", $idsNotFound));

            return $response->setContent($content);
        }

        throw new HttpException(500, 'Country code not found.');
    }

    public function getSubdivisionsForExport(string $locale, int $level): array
    {
        set_time_limit(0);
        $subDivInfos = [];
        $outputCount = 0;
        //______NOTE
        $locales = ["en", "fr", "it", "de", "es", "nl", "pl", "ru", "th", "zh", "ko", "ar", "ja", "tr", "uk", "zh-tw",];
        //__________
        if ($level == 0) {
            $list = $this->entityManager->getRepository(GeonamesCountry::class)->findAll();
        } else {
            $countriesToFetch = [];
            foreach ($this->entityManager->getRepository(GeonamesCountryLevel::class)->findUsedLevelMoreThan($level) as $country) {
                $countriesToFetch[] = $country['countryCode'];
            }
            $list = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findADMsForCountryLevel($level, $countriesToFetch);
        }
        foreach ($list as $subKey => $subDivision) {
            if ($level == 0) {
                if ($localeFound = $this->entityManager->getRepository(
                    GeonamesCountryLocale::class
                )->findLocalesForGeoId(
                    $subDivision->getGeonameId(),
                    $locale
                )) {
                    $name = $localeFound[0]['name'];
                } else if ($locale === 'zh-tw') {
                    $name = $this->entityManager->getRepository(
                        GeonamesCountryLocale::class
                    )->findOneBy(
                        [
                            'locale' => 'zh',
                            'geonameId' => $subDivision->getGeonameId()
                        ]
                    )->getName();
                }
                $outputCount++;
                $this->logger->info(
                    '✅ Adding locale (Country' .
                        ' ' .
                        $outputCount .
                        '/' .
                        count($list) .
                        ' id: ' .
                        $subDivision->getGeonameId() .
                        ') - "' .
                        $name .
                        '" to the file.'
                );
            } else {
                if ($subDivFound = $this->entityManager->getRepository(
                    AdministrativeDivisionLocale::class
                )->findLocalesForGeoId(
                    $subDivision->getGeonameId(),
                    $locale
                )) {
                    $name = $subDivFound->getName();
                } else

                    $name = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)
                        ->findOneByGeonameId(
                            $subDivision->getGeonameId()
                        )->getName();

                $outputCount++;
                $this->logger->info(
                    '✅ Adding locale (ADM' .
                        $level .
                        ' ' .
                        $outputCount .
                        '/' .
                        count($list) .
                        ' id: ' .
                        $subDivision->getGeonameId() .
                        ') - "' .
                        $name .
                        '" to the file.'
                );
            }
            $startPathId = $subDivision->getGeonameId();
            $subDivInfos[$subKey] = [
                'name' => $name,
                'path' => $this->buildExportPath($startPathId, $level, $locale),
                '_geoloc' =>
                [
                    'lat' => (float)$subDivision->getLat(),
                    'lng' => (float)$subDivision->getLng()
                ],
                'code' => $subDivision->{'getAdminCode' . $level}(),
                'country_code' => $subDivision->getCountryCode(),
                'objectID' => (string)$subDivision->getGeonameId()
            ];
            if ($level === 0) {
                $subDivInfos[$subKey]['level'] =
                    (string)$this->entityManager->getRepository(GeonamesCountryLevel::class)->findOneByCountryCode($subDivision->getCountryCode())->getUsedLevel();
                $subDivInfos[$subKey]['code'] = $subDivision->getCountryCode();
            }
            if ($level === 2) {
                $subDivInfos[$subKey]['code1'] = $subDivision->getAdminCode1();
            }
            if ($level === 3) {
                $subDivInfos[$subKey]['code1'] = $subDivision->getAdminCode1();
                $subDivInfos[$subKey]['code2'] = $subDivision->getAdminCode2();
            }
        }
        file_put_contents(__DIR__ . "/../../var/geonames_export_data/subdivisions_" . $level . "_" . $locale . ".json", json_encode($subDivInfos, JSON_PRETTY_PRINT));

        return $subDivInfos;
    }

    private function buildExportPath(int $currentId, int $level, string $locale, string $path = ''): string
    {
        $currentSubDiv = $this->entityManager->getRepository(
            GeonamesAdministrativeDivision::class
        )->findOneByGeonameId($currentId);

        if ($level === 0) {
            $nameFound =
                $this->translationService->findLocaleOrTranslationForId($currentId, $locale)
                ?: $this->entityManager->getRepository(GeonamesCountry::class)->findOneByGeonameId($currentId)->getCountryName();
            $nameFound = str_replace(' ', '+', $nameFound);
            $path = $nameFound . '/' . $path;

            return substr($path, 0, -1);
        }

        $nameFound =
            $this->translationService->findLocaleOrTranslationForId($currentId, $locale)
            ?: $currentSubDiv->getName();
        $nameFound = str_replace(' ', '+', $nameFound);
        $path = $nameFound . '/' . $path;
        $parentSubDiv =
            $this->entityManager->getRepository(
                GeonamesAdministrativeDivision::class
            )->findOneByGeonameId($currentSubDiv->{'getAdminId' . $level - 1}()) ?:
            $this->entityManager->getRepository(
                GeonamesCountry::class
            )->findOneByGeonameId($currentSubDiv->{'getAdminId' . $level - 1}());

        #special case for country "DE" where ADM3 had no ADM2 parent
        #we have to fetch ADM1 grandparent and lessen level by 1
        if (!$parentSubDiv && $level === 3) {
            $parentSubDiv =
                $this->entityManager->getRepository(
                    GeonamesAdministrativeDivision::class
                )->findOneByGeonameId($currentSubDiv->{'getAdminId' . $level - 2}());
            $level--;
        }

        return $this->buildExportPath(
            $parentSubDiv->getGeonameId(),
            $level - 1,
            $locale,
            $path
        );
    }

    public function getSubdivisionsForApi(string $locale, string $countryCode): JsonResponse
    {
        set_time_limit(0);
        $response = new JsonResponse();
        $countryCode = strtoupper($countryCode);

        $apiCacheKey = 'apiAdminDiv_' . $locale . '_' . $countryCode;
        $apiCacheData = $this->redisCache->getItem($apiCacheKey);
        if ($apiCacheData->isHit()) {
            return $response->setContent($apiCacheData->get());
        }
        $countryLevelForApi = $this->entityManager->getRepository(GeonamesCountryLevel::class)->findOneByCountryCode($countryCode)->getUsedLevel();
        $responseContent = [
            'level1' => [],
            'level2' => [],
            'level3' => [],
        ];
        for ($level = 1; $level <= $countryLevelForApi; $level++) {
            $levelList = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findBy(
                ['countryCode' => $countryCode, 'fcode' => 'ADM' . $level]
            );
            $responseContent['level' . $level] = $this->buildApiResponse($levelList, $locale, $countryCode, $level);
        }
        $apiCacheData->set(json_encode($responseContent));
        $this->redisCache->save($apiCacheData);
        $response->setContent(json_encode($responseContent));

        return $response;
    }

    private function buildApiResponse(array $adminDivsForLevel, string $locale, string $countrycode, int $level): array
    {
        $apiLevelResponse = [];

        foreach ($adminDivsForLevel as $adminDiv) {
            $name = $adminDiv->getName();
            if (
                $adminDivLocale = $this->translationService->findLocaleOrTranslationForId($adminDiv->getGeonameId(), $locale)
            ) {
                $name = $adminDivLocale;
            } else if (
                $fallbackLocale = $this->entityManager->getRepository(AdministrativeDivisionLocale::class)->findOneFallBack($adminDiv->getGeonameId(), strtolower($countrycode))
            ) {
                $name = $fallbackLocale[0]->getName();
            }
            if ($level == 1) {
                $adminCodeUp = $countrycode;
            } else {
                $adminCodeUp = $adminDiv->{'getAdminCode' . $level - 1}($this->altCodes);
            }

            $apiLevelResponse[] = [
                'code_up' => $adminCodeUp,
                'code' => $adminDiv->{'getAdminCode' . $level}($this->altCodes),
                'name' => $name,
                'geonameId' => (string)$adminDiv->getGeonameId(),
            ];
        }

        return $apiLevelResponse;
    }
}

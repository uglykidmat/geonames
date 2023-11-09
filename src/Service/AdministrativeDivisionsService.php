<?php

namespace App\Service;

use App\Entity\GeonamesCountry;
use App\Entity\GeonamesTranslation;
use App\Entity\GeonamesCountryLevel;
use App\Entity\GeonamesCountryLocale;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AdministrativeDivisionLocale;
use App\Entity\GeonamesAdministrativeDivision;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\AdministrativeDivisionLocaleService;
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
        private GeonamesDBCachingService $dbservice,
        private GeonamesCountryLevelService $clvlService,
        private GeonamesCountryLocaleService $clService,
        private AdministrativeDivisionLocaleService $admLocService,
        private CacheItemPoolInterface $redisCache,
        private string $redisDsn
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

    public function addAdminDivisions(string $fcode, int $startrow, array|string $countries): JsonResponse
    {
        $response = new JsonResponse();
        $responseContent = '';
        $newEntryCount = 0;
        $entriesFoundCount = 0;
        $adminDivRepository = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class);
        try {
            $apiResult = json_decode($this->apiservice->searchJSON($fcode, $startrow, $countries)->getContent());
        } catch (\Exception $e) {
            throw new BadRequestException('Error during Geonames searchJSON request.');
        }
        if (count($apiResult->geonames) != 0) {
            foreach ($apiResult->geonames as $entry) {
                if (!$adminDivRepository->findOneByGeonameId($entry->geonameId)) {
                    $newAdminDiv = new GeonamesAdministrativeDivision();
                    $newAdminDiv
                        ->setGeonameId($entry->geonameId)
                        ->setName($entry->name)
                        ->setAsciiName($entry->asciiName ?? null)
                        ->setToponymName($entry->toponymName ?? null)
                        ->setContinentCode($entry->continentCode ?? null)
                        ->setCc2($entry->cc2 ?? null)
                        ->setCountryCode($entry->countryCode ?? null)
                        ->setCountryId($entry->countryId ?? null)
                        ->setAdminName1($entry->adminName1 ?? null)
                        ->setAdminName2($entry->adminName2 ?? null)
                        ->setAdminName3($entry->adminName3 ?? null)
                        ->setAdminName4($entry->adminName4 ?? null)
                        ->setAdminName5($entry->adminName5 ?? null)
                        ->setAdminId1($entry->adminId1 ?? null)
                        ->setAdminId2($entry->adminId2 ?? null)
                        ->setAdminId3($entry->adminId3 ?? null)
                        ->setAdminId4($entry->adminId4 ?? null)
                        ->setAdminId5($entry->adminId5 ?? null)
                        ->setAdminCode1($entry->adminCode1 ?? null)
                        ->setAdminCode2($entry->adminCode2 ?? null)
                        ->setAdminCode3($entry->adminCode3 ?? null)
                        ->setAdminCode4($entry->adminCode4 ?? null)
                        ->setLat($entry->lat ?? null)
                        ->setLng($entry->lng ?? null)
                        ->setPopulation($entry->population ?? null)
                        ->setTimezoneGmtOffset($entry->timezone->gmtOffset ?? null)
                        ->setTimezoneTimeZoneId($entry->timezone->timeZoneId ?? null)
                        ->setTimezoneDstOffset($entry->timezone->dstOffset ?? null)
                        ->setAdminTypeName($entry->adminTypeName ?? null)
                        ->setFcode($entry->fcode ?? null)
                        ->setFcl($entry->fcl ?? null)
                        ->setSrtm3($entry->srtm3 ?? null)
                        ->setAstergdem($entry->astergdem ?? null);

                    $this->entityManager->persist($newAdminDiv);

                    $responseContent .= $entry->name . ', ';
                    $newEntryCount++;
                } else {
                    $entriesFoundCount++;
                }
            }
            $this->entityManager->flush();
        }
        $result = ['Status' => 'Success', 'Entries found' => $entriesFoundCount + 1, 'New entries' => $newEntryCount, 'Max entries for this fcode' => $apiResult->totalResultsCount];

        $response->setContent(json_encode($result));

        return $response;
    }

    public function updateAlternativeCodes(): JsonResponse
    {
        $response = new JsonResponse();
        $content = [];
        if ($altCodesList = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_alternative_divisions.json'))) {
            foreach ($altCodesList as $altCode) {
                if ($adminDiv = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId($altCode->geonameId)) {

                    $adminDiv->setAdminCodeAlt1($altCode->adminCodes1 ?? null)
                        ->setAdminCodeAlt2($altCode->adminCodes2 ?? null)
                        ->setAdminCodeAlt3($altCode->adminCodes3 ?? null);
                    $this->entityManager->persist($adminDiv);
                    $content[] = [$adminDiv->getGeonameId() => 'new alt code ' . $altCode->adminCodes1];
                }
            }
            $this->entityManager->flush();

            return $response->setContent(json_encode($content));
        }

        throw new HttpException(500, 'Country code not found.');
    }

    public function getSubdivisionsForExport(string $locale, int $level): array
    {
        $locales = ["en", "fr", "it", "de", "es", "nl", "pl", "ru", "th", "zh", "ko", "ar", "ja", "tr", "uk", "zh-tw",];

        if ($level == 0) {
            $list = $this->entityManager->getRepository(GeonamesCountry::class)->findAll();
        } else $list = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findByFcode('ADM' . $level);

        foreach ($list as $subDivision) {
            if ($translation = $this->entityManager->getRepository(
                GeonamesTranslation::class
            )->findOneByGeonameId(
                $subDivision->getGeonameId()
            )) {
                $name = $translation->getName();
            }

            // else if 

            // {
            //     $name = $this->entityManager->getRepository(GeonamesCountryLocale::class)->findLocalesForGeoId($subDivision->getGeonameId(), $locale)[0]['name'];
            //     //dd($this->entityManager->getRepository(GeonamesCountryLocale::class)->findLocalesForGeoId($subDivision->getGeonameId(), $locale));
            // }

            $subDivInfos[] = [
                'name' => $name,
                'path' => $name,
                '_geoloc' =>
                [
                    'lat' => (float)$subDivision->getLat(),
                    'lng' => (float)$subDivision->getLng()
                ],
                'level' => (string)$this->entityManager->getRepository(GeonamesCountryLevel::class)->findOneByCountryCode($subDivision->getCountryCode())->getUsedLevel(),
                'code' => $subDivision->getCountryCode(),
                'country_code' => $subDivision->getCountryCode(),
                'objectID' => (string)$subDivision->getGeonameId()
            ];
        }
        //dd(__DIR__);
        file_put_contents(__DIR__ . "/../../var/geonames_export_data/subdivisions_" . $level . "_" . $locale . ".json", json_encode($subDivInfos, JSON_PRETTY_PRINT));

        return $subDivInfos;
    }

    public function getSubdivisionsForApi(string $locale, string $countrycode): JsonResponse
    {
        set_time_limit(0);
        $response = new JsonResponse();

        $apiCacheKey = 'apiAdminDiv_' . $locale . '_' . $countrycode;
        $apiCacheData = $this->redisCache->getItem($apiCacheKey);
        if ($apiCacheData->isHit()) {
            return $response->setContent($apiCacheData->get());
        }

        $responseContent = [];
        for ($level = 1; $level <= 3; $level++) {
            $levelList = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findBy(
                ['countryCode' => $countrycode, 'fcode' => 'ADM' . $level]
            );

            $responseContent['level' . $level] = $this->buildApiResponse($levelList, $locale, $countrycode, $level);
        }

        $apiCacheData->set(json_encode($responseContent));
        $this->redisCache->save($apiCacheData);
        $response->setContent(json_encode($responseContent));

        return $response;
    }

    public function buildApiResponse(array $adminDivsForLevel, string $locale, string $countrycode, int $level): array
    {
        $apiLevelResponse = [];

        foreach ($adminDivsForLevel as $adminDiv) {
            // default
            $name = $adminDiv->getName();

            if ($adminDivLocale = $this->entityManager->getRepository(AdministrativeDivisionLocale::class)->findOneBy(
                ['geonameId' => $adminDiv->getGeonameId(), 'locale' => $locale]
            )) {
                $name = $adminDivLocale->getName();
            } else if ($fallbackLocale = $this->entityManager->getRepository(AdministrativeDivisionLocale::class)->findOneFallBack($adminDiv->getGeonameId(), strtolower($countrycode))) {
                $name = $fallbackLocale->getName();
            }

            $apiLevelResponse[] = [
                'code_up' => $countrycode,
                'code' => $adminDiv->{'getAdminCode' . $level}($this->altCodes),
                'name' => $name,
                'geonameId' => (string)$adminDiv->getGeonameId(),
            ];
        }

        return $apiLevelResponse;
    }
}

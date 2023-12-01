<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AdministrativeDivisionLocale;
use App\Entity\GeonamesAdministrativeDivision;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdministrativeDivisionLocaleService
{
    public function __construct(
        public GeonamesAPIServiceInterface $apiservice,
        public EntityManagerInterface $entityManager,
        private CacheItemPoolInterface $redisCache,
        private string $redisDsn,
        private LoggerInterface $logger,
    ) {
    }

    public function showSubdivisionsLocales(string $countrycode, string $locale, string $fcode): JsonResponse
    {
        $response = new JsonResponse();
        $output = [];
        $subDivLocales = $this->entityManager->getRepository(AdministrativeDivisionLocale::class)->findBy(
            [
                'countryCode' => $countrycode,
                'locale' => $locale,
                'fCode' => $fcode
            ]
        );
        foreach ($subDivLocales as $locale) {
            $output[] = ['name' => $locale->getName(), 'lang' => $locale->getFcode(), 'country' => $locale->getCountryCode()];
        }
        $response->setContent(json_encode($output));

        return $response;
    }

    public function updateSubdivisionsLocales(array $countrycodes): JsonResponse
    {
        set_time_limit(0);
        $response = new JsonResponse();
        $totalLocalesCount = 0;

        foreach ($countrycodes as $countrycode) {
            $adminDivsList = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findByCountryCode(
                strtoupper($countrycode)
            );
            $this->logger->info(
                '🌍 Getting locales for country code ' .
                    $countrycode . ' 🌏'
            );
            $divsCount = 0;
            $totalDivsCount = count($adminDivsList);
            foreach ($adminDivsList as $adminDiv) {
                $totalLocalesCount += $this->getSubdivisionsLocalesForId($adminDiv->getGeonameId());
                $divsCount++;
                $this->logger->info(
                    '👉 ::' .
                        $divsCount . '/' .
                        $totalDivsCount .
                        ' :: Getting locales for ' .
                        $adminDiv->getFcode() . ' - ' .
                        $adminDiv->getGeonameId() . ' (' .
                        $adminDiv->getName() . ')'
                );
            }
            #Sometimes the geonames API is in the cabbages, a small delay in the next requests can help
            usleep(500000);
        }
        $response->setContent(
            json_encode([
                'Status' => 'Success',
                'New locales' => $totalLocalesCount
            ])
        );

        return $response;
    }

    public function getSubdivisionsLocalesForId(int $geonameId): int
    {
        $newLocalesCount = 0;
        $apiResponse = $this->apiservice->getJsonSearch($geonameId);

        if (isset($apiResponse->alternateNames)) {
            foreach ($apiResponse->alternateNames as $localeItem) {
                #we exlude the wkdt+link(wikipedia) languages, as well as 'lauc' (?) and 'abbr'(abbreviation) data from fetching
                if (isset($localeItem->lang) &&  !in_array($localeItem->lang, ['link', 'lauc', 'abbr', 'wkdt', 'unlc'])) {
                    if (!$this->entityManager->getRepository(AdministrativeDivisionLocale::class)
                        ->findOneBy(array(
                            'geonameId' => $geonameId,
                            'locale' => strtolower($localeItem->lang)
                        ))) {
                        $newLocale = new AdministrativeDivisionLocale();
                        $newLocale
                            ->setGeonameId($geonameId)
                            ->setLocale($localeItem->lang)
                            ->setCountryCode($apiResponse->countryCode)
                            ->setFCode($apiResponse->fcode)
                            ->setName($localeItem->name)
                            ->setIsPreferredName($localeItem->isPreferredName ?? null)
                            ->setIsShortName($localeItem->isShortName ?? null);
                        $this->entityManager->persist($newLocale);
                        $this->logger->info('  ✅ Locale added.', ['Language' => $localeItem->lang]);
                        $newLocalesCount++;
                    } else $this->logger->info('  ❕ Locale already found.', ['Language' => $localeItem->lang]);
                } else $this->logger->info('  🚭 No locales available or excluded by the script.');
            }
            $this->entityManager->flush();
        } else {
            $errMsg = json_decode($apiResponse);
            $this->logger->warning(
                '  ❌ ' .
                    $$errMsg['status']['message']
            );
        }

        return $newLocalesCount;
    }
}

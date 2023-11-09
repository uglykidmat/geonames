<?php

namespace App\Service;

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

    public function updateSubdivisionsLocales(string $countrycode): JsonResponse
    {
        set_time_limit(0);
        $response = new JsonResponse();
        $totalLocalesCount = 0;
        $adminDivsList = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findByCountryCodeADM(
            $countrycode
        );

        foreach ($adminDivsList as $adminDiv) {
            $totalLocalesCount += self::getSubdivisionsLocalesForId($adminDiv->getGeonameId());
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
        foreach ($apiResponse->alternateNames as $localeItem) {
            if (isset($localeItem->lang) && $localeItem->lang !== 'link') {
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
                    $newLocalesCount++;
                }
            }
        }
        $this->entityManager->flush();

        return $newLocalesCount;
    }
}

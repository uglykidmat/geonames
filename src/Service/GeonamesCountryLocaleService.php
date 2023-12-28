<?php

namespace App\Service;

use App\Entity\GeonamesTranslation;
use App\Entity\GeonamesCountryLocale;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GeonamesCountryLocaleService
{
    public function __construct(
        public GeonamesAPIServiceInterface $apiservice,
        public EntityManagerInterface $entityManager,
        private CacheItemPoolInterface $redisCache,
        private string $redisDsn,
    ) {
    }

    public function updateCountryBatch(int $file): string
    {
        if ($idsList = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountriesGeonameIds_' . $file . '.json'))) {
            $outputCount = 0;
            foreach ($idsList as $geonameId => $uselessvalue) {
                $countryResponse = $this->apiservice->getJsonSearch($geonameId);
                foreach ($countryResponse->alternateNames as $countryLang) {
                    if (isset($countryLang->lang) &&  !in_array($countryLang->lang, ['link', 'lauc', 'abbr', 'wkdt'])) {
                        if (!$this->entityManager->getRepository(GeonamesCountryLocale::class)
                            ->findOneBy(array(
                                'geonameId' => $geonameId,
                                'locale' => strtolower($countryLang->lang)
                            ))) {
                            $newCountryLocale = new GeonamesCountryLocale();
                            $newCountryLocale
                                ->setGeonameId($geonameId)
                                ->setCountryCode($countryResponse->countryCode)
                                ->setName($countryLang->name)
                                ->setLocale(strtolower($countryLang->lang))
                                ->setIsPreferredName($countryLang->isPreferredName ?? null)
                                ->setIsShortName($countryLang->isShortName ?? null);
                            $this->entityManager->persist($newCountryLocale);
                            $outputCount++;
                        }
                    }
                }
                $this->entityManager->flush();
            }
            if ($outputCount == 0) {
                (string)$outputCount = "all elements in this file have already been imported";
            }
            return json_encode(['ImportsDone' => $outputCount]);
        } else throw new HttpException(400, 'Invalid request : file number ' . $file . ' does not exist');
    }

    public function updateCountrySingle(int $geonameId): string
    {
        $countryResponse = $this->apiservice->getJsonSearch($geonameId);
        $output = '';

        foreach ($countryResponse->alternateNames as $countryLang) {
            if (isset($countryLang->lang)) {
                if (!$this->entityManager->getRepository(GeonamesCountryLocale::class)
                    ->findOneBy(array(
                        'geonameId' => $geonameId,
                        'locale' => strtolower($countryLang->lang)
                    ))) {
                    $newCountryLocale = new GeonamesCountryLocale();
                    $newCountryLocale
                        ->setGeonameId($geonameId)
                        ->setCountryCode($countryResponse->countryCode)
                        ->setName($countryLang->name)
                        ->setLocale(strtolower($countryLang->lang))
                        ->setIsPreferredName($countryLang->isPreferredName ?? null)
                        ->setIsShortName($countryLang->isShortName ?? null);
                    $this->entityManager->persist($newCountryLocale);

                    $output .= $newCountryLocale->getCountryCode() .
                        '/' .
                        $newCountryLocale->getLocale() . ',';
                }
            }
        }
        $this->entityManager->flush();
        if (empty($output)) {
            (string)$output = 'The ID ' . $geonameId . ' has already been imported';
        }
        return $output;
    }

    public function getCountryNamesForLocale(string $locale): JsonResponse
    {
        $response = new JsonResponse();
        $localesCacheKey = 'locales_' . $locale;
        $localesCachedData = $this->redisCache->getItem($localesCacheKey);
        if ($localesCachedData->isHit()) {
            return $response->setContent($localesCachedData->get());
        } else {
            $baseLocales = $this->entityManager->getRepository(GeonamesCountryLocale::class)
                ->findLocales($locale);
            if ($translationOverrides = $this->entityManager->getRepository(GeonamesTranslation::class)
                ->findBy(
                    ['locale' => $locale, 'fcode' => 'COUNTRY']
                )
            ) {
                foreach ($baseLocales as $baseKey => $baseValue) {
                    foreach ($translationOverrides as $overrideKey => $overrideValue) {
                        if (
                            $baseValue['geonameId'] == $overrideValue->getGeonameId()
                        ) {
                            unset($baseLocales[$baseKey]);
                            $newValue['geonameId'] = $overrideValue->getGeonameId();
                            $newValue['country_code'] = $overrideValue->getCountryCode();
                            $newValue['name'] = $overrideValue->getName();
                            $baseLocales[] = $newValue;
                            unset($translationOverrides[$overrideKey]);
                        }
                    }
                }

                foreach ($translationOverrides as $overrideValue) {
                    $newValue['geonameId'] = $overrideValue->getGeonameId();
                    $newValue['country_code'] = $overrideValue->getCountryCode();
                    $newValue['name'] = $overrideValue->getName();
                    $baseLocales[] = $newValue;
                }
            }
        }
        $localesCachedData->set(json_encode(array_values($baseLocales)));
        $this->redisCache->save($localesCachedData);

        return $response->setContent(json_encode(array_values($baseLocales)));
    }

    public function findCountryCodeByName(string $name): array
    {
        return $this->entityManager->getRepository(GeonamesCountryLocale::class)->findCountryCodeByName($name);
    }
}

<?php

namespace App\Service;

use App\Entity\GeonamesCountryLocale;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeonamesCountryLocaleService
{
    public function __construct(
        public GeonamesAPIServiceInterface $apiservice,
        public EntityManagerInterface $entityManager
    ) {
    }

    public function updateCountryBatch(int $file): string
    {
        $output = '';
        switch ($file) {
            case 1:
                $idsList = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountriesGeonameIds_1.json'));
                break;
            case 2:
                $idsList = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountriesGeonameIds_2.json'));
                break;
            case 3:
                $idsList = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountriesGeonameIds_3.json'));
                break;
            case 4:
                $idsList = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountriesGeonameIds_4.json'));
                break;
        }

        foreach ($idsList as $geonameId => $uselessvalue) {

            $countryResponse = $this->apiservice->getJsonSearch($geonameId);
            $countryLangs = $countryResponse->alternateNames;

            foreach ($countryLangs as $countryLang) {
                if (isset($countryLang->lang)) {
                    if (!$this->entityManager->getRepository(GeonamesCountryLocale::class)
                        ->findOneBy(array(
                            'geonameId' => $geonameId,
                            'locale' => $countryLang->lang
                        ))) {
                        $newCountryLocale = new GeonamesCountryLocale();
                        $newCountryLocale
                            ->setGeonameId($geonameId)
                            ->setCountryCode($countryResponse->countryCode)
                            ->setName($countryLang->name)
                            ->setLocale($countryLang->lang);
                        $this->entityManager->persist($newCountryLocale);
                        $output .= $newCountryLocale->getCountryCode() .
                            '/' .
                            $newCountryLocale->getLocale() . ',';
                    }
                }
            }
            $this->entityManager->flush();
        }
        if ($output == '') {
            $output = "all elements in this file have already been imported";
        }
        return $output;
    }

    public function updateCountrySingle(int $geonameId): string
    {
        $countryResponse = $this->apiservice->getJsonSearch($geonameId);
        $countryLangs = $countryResponse->alternateNames;
        $output = '';
        foreach ($countryLangs as $countryLang) {
            if (isset($countryLang->lang)) {
                if (!$this->entityManager->getRepository(GeonamesCountryLocale::class)
                    ->findOneBy(array(
                        'geonameId' => $geonameId,
                        'locale' => $countryLang->lang
                    ))) {
                    $newCountryLocale = new GeonamesCountryLocale();
                    $newCountryLocale
                        ->setGeonameId($geonameId)
                        ->setCountryCode($countryResponse->countryCode)
                        ->setName($countryLang->name)
                        ->setLocale($countryLang->lang);
                    $this->entityManager->persist($newCountryLocale);

                    $output .= $newCountryLocale->getCountryCode() .
                        '/' .
                        $newCountryLocale->getLocale() . ',';
                }
            }
        }
        $this->entityManager->flush();
        return $output;
    }

    public function getCountryNamesForLocale($locale): JsonResponse
    {
        $response = new JsonResponse();
        $content = [];

        foreach ($this->entityManager->getRepository(GeonamesCountryLocale::class)
            ->findBy(
                array('locale' => $locale),
                array('countryCode' => 'ASC')
            ) as $countryLocale) {
            $entry['countryCode'] = $countryLocale->getCountryCode();

            $entry['geonameId'] = $countryLocale->getGeonameId();
            $entry['name'] = $countryLocale->getName();
            $content[] = $entry;
        }
        return $response->setContent(json_encode($content));
    }
}

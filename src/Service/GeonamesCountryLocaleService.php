<?php

namespace App\Service;

use App\Entity\GeonamesCountryLocale;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GeonamesCountryLocaleService
{
    public function __construct(
        public GeonamesAPIServiceInterface $apiservice,
        public EntityManagerInterface $entityManager
    ) {
    }

    public function updateCountryBatch(int $file): string
    {
        $output = [];
        if ($idsList = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountriesGeonameIds_' . $file . '.json'))) {

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

                            $output[] = [$newCountryLocale->getCountryCode() => $newCountryLocale->getLocale()];
                        }
                    }
                }

                $this->entityManager->flush();
            }
            if (empty($output)) {
                (string)$output = "all elements in this file have already been imported";
            }
            return json_encode($output);
        } else throw new HttpException(400, 'Invalid request : file number ' . $file . ' does not exist');
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

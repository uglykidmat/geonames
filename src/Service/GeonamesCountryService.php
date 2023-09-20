<?php

namespace App\Service;

use App\Entity\GeonamesCountry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesCountryService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $token,
        public EntityManagerInterface $entityManager
    ) {
    }

    public function purgeCountryList(): JsonResponse
    {
        $countriesListJSON = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountries.json'));
        $repository = $this->entityManager->getRepository(GeonamesCountry::class);
        $response = new JsonResponse();
        $purgeList = [];
        foreach ($countriesListJSON as $countryCode => $uselessValue) {
            if ($country = $repository->findOneByCountryCode($countryCode)) {
                $this->entityManager->remove($country);
                $purgeList[] = $countryCode;
            }
        }
        $this->entityManager->flush();

        return $response->setContent(implode(',', $purgeList));
    }

    public function getGeoCountryList(): JsonResponse
    {
        $countriesInfoUrl = 'http://api.geonames.org/countryInfoJSON?username=' . $this->token;
        $apiresponse = $this->client->request('GET', $countriesInfoUrl);
        $response = new JsonResponse();
        if ($apiresponse->getStatusCode() == 200) {
            $countryList = json_decode($apiresponse->getContent());
            $updateList = [];
            foreach ($countryList->geonames as $countryEntry) {
                $country = new GeonamesCountry();
                $country
                    ->setContinent($countryEntry->continent)
                    ->setCapital($countryEntry->capital)
                    ->setLanguages($countryEntry->languages)
                    ->setGeonameId($countryEntry->geonameId)
                    ->setSouth($countryEntry->south)
                    ->setNorth($countryEntry->north)
                    ->setEast($countryEntry->east)
                    ->setWest($countryEntry->west)
                    ->setIsoAlpha3($countryEntry->isoAlpha3)
                    ->setFipsCode($countryEntry->fipsCode)
                    ->setPopulation($countryEntry->population)
                    ->setIsoNumeric($countryEntry->isoNumeric)
                    ->setAreaInSqKm($countryEntry->areaInSqKm)
                    ->setCountryCode($countryEntry->countryCode)
                    ->setCountryName($countryEntry->countryName)
                    ->setContinentName($countryEntry->continentName)
                    ->setCurrencyCode($countryEntry->currencyCode);
                $this->entityManager->persist($country);
                $updateList[] = $country->getCountryCode();
            }
            $this->entityManager->flush();

            return $response->setContent(implode(',', $updateList));
        } else throw new HttpException("Could not get country list from Geonames");
    }
}

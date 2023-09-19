<?php

namespace App\Service;

use App\Entity\GeonamesCountry;
use Doctrine\ORM\EntityManagerInterface;
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

    public function purgeCountryList(): void
    {
        $countriesListJSON = json_decode(file_get_contents(__DIR__ . '/../../all_countries_data/allCountries.json'));
        $repository = $this->entityManager->getRepository(GeonamesCountry::class);

        foreach ($countriesListJSON as $countryCode => $uselessValue) {
            if ($country = $repository->findOneByCountryCode($countryCode)) {
                $this->entityManager->remove($country);
            }
        }
        $this->entityManager->flush();
    }

    public function getGeoCountryList(): void
    {
        $countriesInfoUrl = 'http://api.geonames.org/countryInfoJSON?username=' . $this->token;
        $response = $this->client->request('GET', $countriesInfoUrl);

        if ($response->getStatusCode() == 200) {
            $countryList = json_decode($response->getContent());

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
            }
            $this->entityManager->flush();
        } else throw new HttpException("Could not get country list from Geonames");
    }
}

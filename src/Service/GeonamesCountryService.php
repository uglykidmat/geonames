<?php

namespace App\Service;

use App\Entity\GeonamesCountry;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesTranslationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GeonamesCountryService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $token,
        public EntityManagerInterface $entityManager,
        private GeonamesTranslationService $translationService,
        private CacheItemPoolInterface $redisCache,
        private string $redisDsn,
    ) {
    }

    public function listCountries(string $locale): array
    {
        $countryListCacheKey = 'countryList_' . $locale;
        $countryListData = $this->redisCache->getItem($countryListCacheKey);

        if ($countryListData->isHit()) {
            return $countryListData->get();
        }
        $listCountries = [];
        foreach ($this->entityManager->getRepository(GeonamesCountry::class)->findAll() as $country) {
            $listCountries[] = [
                'countryCode' => $country->getCountryCode(),
                'geonameId' => $country->getGeonameId(),
                'name' => $this->translationService->findLocaleOrTranslationForId($country->getGeonameId(), $locale)
            ];
        }
        $countryListData->set($listCountries);
        $this->redisCache->save($countryListData);
        dd($listCountries);
        return $listCountries;
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
        } else throw new HttpException(500, "Could not get country list from Geonames");
    }

    public function updateBarycenters(): Response
    {
        if ($countryBarycenters = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_country_barycenters.json'))) {

            foreach ($countryBarycenters as $barycenter) {
                $country = $this->entityManager->getRepository(GeonamesCountry::class)->findOneBy(['geonameId' => $barycenter->geonameId])
                    ->setLat((float)$barycenter->lat)
                    ->setLng((float)$barycenter->lng);
                $this->entityManager->persist($country);
            }
            $this->entityManager->flush();

            return new Response('OK.');
        }
        throw new HttpException(400, 'Missing base file.');
    }

    public function computeBarycenter(string $countryCode): Response
    {
        if ($country = $this->entityManager->getRepository(GeonamesCountry::class)->findOneBy(['countryCode' => $countryCode])) {

            $west = deg2rad($country->getWest());
            $east = deg2rad($country->getEast());
            $south = deg2rad($country->getSouth());
            $north = deg2rad($country->getNorth());

            $centroid_x = ($east + $west) / 2;
            $centroid_y = ($north + $south) / 2;

            $country->setLat(rad2deg($centroid_y))->setLng(rad2deg($centroid_x));
            $this->entityManager->persist($country);
            $this->entityManager->flush();

            return new Response('Barycenter ok for ' . $country->getCountryCode() . '.');
        }

        throw new HttpException(500, 'Country code not found.');
    }
}

<?php

namespace App\Service;

use App\Entity\GeonamesAdministrativeDivision;
use App\Entity\GeonamesCountry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeojsonService
{
    public function __construct(
        public EntityManagerInterface $entityManager,
    ) {
    }

    public function updateAll(): JsonResponse
    {
        $response = new JsonResponse();
        $outputOK = [];
        $outputKO = [];
        if ($geojsonList = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_geojson.json'))) {
            foreach ($geojsonList as $geojson) {
                if ($geojson->fcode == 'COUNTRY') {
                    if ($country = $this->entityManager->getRepository(GeonamesCountry::class)->findOneByGeonameId($geojson->geonameId)) {
                        $country->setGeojson($geojson->geojson);
                        $this->entityManager->persist($country);
                        $outputOK[] = [$geojson->geonameId => 'OK'];
                    } else $outputKO[] = [$geojson->geonameId => 'KO'];
                } else {
                    if ($adminDiv = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId($geojson->geonameId)) {
                        $adminDiv->setGeojson($geojson->geojson);
                        $this->entityManager->persist($adminDiv);
                        $outputOK[] = [$geojson->geonameId => 'OK'];
                    } else $outputKO[] = [$geojson->geonameId => 'KO'];
                }
            }
            $this->entityManager->flush();
        }

        $response->setContent(json_encode(['GeonameIDs OK' => $outputOK, 'GeonameIDs KO' => $outputKO]));
        return $response;
    }

    public function getAll(): JsonResponse
    {
        $response = new JsonResponse();
        $output = [];

        foreach ($this->entityManager->getRepository(GeonamesCountry::class)->findGeoJson() as $entry) {
            $country = [
                'geonameId' => $entry->getGeonameId(),
                'name' => $entry->getCountryName(),
                'geojson' => $entry->getGeojson(),
            ];
            $output[] = $country;
        }

        foreach ($this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findGeoJson() as $entry) {

            $adminDiv = [
                'geonameId' => $entry->getGeonameId(),
                'name' => $entry->getName(),
                'geojson' => $entry->getGeojson(),
            ];

            $output[] = $adminDiv;
        }
        $response->setContent(json_encode($output));

        return $response;
    }

    public function getOne(int $id): JsonResponse
    {
        $response = new JsonResponse();
        if ($idfound = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId($id)) {
            $result = [
                'geonameId' => $idfound->getGeonameId(),
                'name' => $idfound->getName(),
                'geojson' => $idfound->getGeojson(),
            ];

            return $response->setContent(json_encode($result));
        } else if ($idfound = $this->entityManager->getRepository(GeonamesCountry::class)->findOneByGeonameId($id)) {
            $result = [
                'geonameId' => $idfound->getGeonameId(),
                'name' => $idfound->getCountryName(),
                'geojson' => $idfound->getGeojson(),
            ];
            return $response->setContent(json_encode($result));
        }

        return $response->setContent(json_encode(['Status' => 'Not found']));
    }
}

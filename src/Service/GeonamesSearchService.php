<?php
// src/Service/GeonamesSearchService.php
namespace App\Service;

use stdClass;
use App\Service\GeonamesAPIService;
use App\Entity\GeonamesCountryLevel;
use Psr\Cache\CacheItemPoolInterface;
use App\Service\AdminCodesMapperService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GeonamesAdministrativeDivisionRepository;

class GeonamesSearchService
{
    public function __construct(
        private GeonamesAPIService $apiService,
        private GeonamesDBCachingService $dbCachingService,
        private GeonamesAdministrativeDivisionRepository $gRepository,
        private EntityManagerInterface $entityManager,
        private CacheItemPoolInterface $redisCache,
        private AdminCodesMapperService $adminCodesMapperService,
        private string $redisDsn
    ) {
        $this->redisCache = $redisCache;
    }

    public function bulkRequest(?string $request): string
    {
        $bulkResponse = json_decode($request);
        $gclRepository = $this->entityManager->getRepository(GeonamesCountryLevel::class);

        foreach ($bulkResponse as $bulkIndex => $bulkRow) {

            if (self::checkRequestContents($bulkRow) == "coordinates") {

                $cacheKey = 'geonames_latlng_' .
                    $bulkRow->lat .
                    '-' .
                    $bulkRow->lng;

                $cachedData = $this->redisCache->getItem($cacheKey);

                if (!$cachedData->isHit()) {
                    $geoIdFound = $this->apiService->latLngSearch(
                        $bulkRow->lat,
                        $bulkRow->lng
                    );
                    if (!$IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geoIdFound)) {
                        $idToSave = $this->apiService->getJsonSearch($geoIdFound);

                        $this->dbCachingService->saveSubdivisionToDatabase($idToSave);
                    }

                    $UsedLevel = $gclRepository->findOneByCountryCode(
                        $IdFoundInDb->getCountryCode()
                    )->getUsedLevel();

                    $adminCodesArray = $this->adminCodesMapperService->codesMapper($IdFoundInDb, $UsedLevel);

                    $bulkResponse[$bulkIndex] = [
                        ...(array)$bulkResponse[$bulkIndex],
                        ...['error' => false],
                        ...['used_level' => $UsedLevel],
                        ...['country_code' => $IdFoundInDb->getCountryCode()],
                        ...$adminCodesArray
                    ];

                    $cachedData->set($bulkResponse[$bulkIndex]);
                    $this->redisCache->save($cachedData);
                } else {
                    $bulkResponse[$bulkIndex] = $cachedData->get();
                }
            } else if (self::checkRequestContents($bulkRow) == "zipcode") {

                $cacheKey = 'geonames_country-zipcode_' .
                    $bulkRow->country_code .
                    "-" .
                    $bulkRow->zip_code;

                $cachedData = $this->redisCache->getItem($cacheKey);

                if (!$cachedData->isHit()) {
                    $geonamesZipCodeFound = $this->apiService->postalCodeLookupJSON(
                        $bulkRow->zip_code,
                        $bulkRow->country_code
                    );

                    $geoIdFound = $this->apiService->latLngSearch(
                        $geonamesZipCodeFound['postalcodes'][0]['lat'],
                        $geonamesZipCodeFound['postalcodes'][0]['lng']
                    );

                    if (!$IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geoIdFound)) {
                        $idToSave = $this->apiService->getJsonSearch($geoIdFound);
                        $this->dbCachingService->saveSubdivisionToDatabase($idToSave);
                    }
                    $IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geoIdFound);

                    $UsedLevel = $gclRepository->findOneByCountryCode(
                        $IdFoundInDb->getCountryCode()
                    )->getUsedLevel();

                    $adminCodesArray = $this->adminCodesMapperService->codesMapper($IdFoundInDb, $UsedLevel);

                    $bulkResponse[$bulkIndex] = [
                        ...(array)$bulkResponse[$bulkIndex],
                        ...['error' => false],
                        ...['lat' => $IdFoundInDb->getLat(), 'lng' => $IdFoundInDb->getLng()],
                        ...['used_level' => $UsedLevel],
                        ...['country_code' => $IdFoundInDb->getCountryCode()],
                        ...$adminCodesArray
                    ];

                    $cachedData->set($bulkResponse[$bulkIndex]);
                    $this->redisCache->save($cachedData);
                } else {
                    $bulkResponse[$bulkIndex] = $cachedData->get();
                }
            } else {
                $bulkResponse[$bulkIndex] = [
                    ...(array)$bulkResponse[$bulkIndex],
                    ...['error' => true, 'message' => 'Not found by lat-lng coordinates, nor Country-ZipCode']
                ];
            }
        }

        return json_encode($bulkResponse);
    }

    private function checkRequestContents(stdClass $requestEntry): string
    {
        if (
            !empty($requestEntry->lat)
            && is_numeric($requestEntry->lat)
            && !empty($requestEntry->lat)
            && is_numeric($requestEntry->lat)
        ) {
            return "coordinates";
        } else if (
            !empty($requestEntry->country_code)
            && is_string($requestEntry->country_code)
            && !empty($requestEntry->zip_code)
            && is_string($requestEntry->zip_code)
            && $this->entityManager
            ->getRepository(GeonamesCountry::class)
            ->findByCountryCode($requestEntry->country_code)
        ) {
            return "zipcode";
        }

        return "Missing or incorrect fields";
    }
}

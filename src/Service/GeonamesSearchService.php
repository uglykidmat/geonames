<?php
// src/Service/GeonamesSearchService.php
namespace App\Service;

use App\Entity\GeonamesAdministrativeDivision;
use stdClass;
use App\Entity\GeonamesCountry;
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
        $this->gclRepository = $this->entityManager->getRepository(GeonamesCountryLevel::class);
    }

    public function bulkRequest(?string $request): string
    {
        $bulkResponse = [];
        $bulkRequest = json_decode($request);

        foreach ($bulkRequest as $bulkIndex => $bulkRow) {

            // defaults output values
            $bulkResponse[$bulkIndex] = [
                ...(array)$bulkRow,
                'error' => false,
                'used_level' => 1,
            ];

            $requestType = self::checkRequestContents($bulkRow);
            if (!$requestType) {
                // set error for this row
                $bulkResponse[$bulkIndex]['error'] = true;
                $bulkResponse[$bulkIndex]['message'] = 'Not found by lat-lng coordinates, nor Country-ZipCode';
                // return error & jump next row
                continue;
            }

            // first get redis cache if exist
            $cacheKey = $this->getCacheKey($requestType, $bulkRow);
            if (($geodata = $this->hasCache($cacheKey))) {
                // set response for this row
                $bulkResponse[$bulkIndex] = [
                    'elt_id' => $bulkRow->elt_id ?? $bulkIndex,
                    ...$geodata,
                ];
                // return cache & jump next row
                continue;
            }

            // get
            switch ($requestType) {
                case 'coordinates':
                    $geodata = $this->getByCoodinates($bulkRow);
                    break;
                case 'zipcode':
                    $geodata = $this->getByZipcode($bulkRow);
                    break;
                default:
                    // this case should never append due to previous $requestType error check
                    $geodata['error'] = true;
            }

            // set for next iteration
            $this->setCache($cacheKey, $geodata);

            // set response for this row
            $bulkResponse[$bulkIndex] = [
                'elt_id' => $bulkRow->elt_id ?? $bulkIndex,
                ...$geodata,
            ];
        }

        return json_encode($bulkResponse, JSON_THROW_ON_ERROR);
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

        return false;
    }

    public function getByCoodinates(stdClass $bulkRow): array
    {
        $geoIdFound = $this->apiService->latLngSearch(
            $bulkRow->lat,
            $bulkRow->lng
        );

        $geoDivision = $this->getGeoDivision($geoIdFound);
        $UsedLevel = $this->getLevel($geoDivision);
        $adminCodesArray = $this->adminCodesMapperService->codesMapper($geoDivision, $UsedLevel);

        return [
            'used_level' => $UsedLevel,
            'country_code' => $geoDivision->getCountryCode(),
            ...$adminCodesArray
        ];
    }

    public function getByZipcode(mixed $bulkRow): array
    {
        $geonamesZipCodeFound = $this->apiService->postalCodeLookupJSON(
            $bulkRow->zip_code,
            $bulkRow->country_code
        );

        $geoIdFound = $this->apiService->latLngSearch(
            $geonamesZipCodeFound['postalcodes'][0]['lat'],
            $geonamesZipCodeFound['postalcodes'][0]['lng']
        );

        $geoDivision = $this->getGeoDivision($geoIdFound);
        $UsedLevel = $this->getLevel($geoDivision);
        $adminCodesArray = $this->adminCodesMapperService->codesMapper($geoDivision, $UsedLevel);

        return [
            'lat' => $geoDivision->getLat(),
            'lng' => $geoDivision->getLng(),
            'used_level' => $UsedLevel,
            'country_code' => $geoDivision->getCountryCode(),
            ...$adminCodesArray
        ];
    }

    // TODO : externalize those method in CacheRedisService
    public function getCacheKey(string $requestType, stdClass $bulkRow): string
    {
        switch ($requestType) {
            case 'coordinates':
                $cacheKey = 'geonames_latlng_' . $bulkRow->lat . '-' . $bulkRow->lng;
                break;
            case 'zipcode':
                $cacheKey = 'geonames_country-zipcode_' . $bulkRow->country_code . "-" . $bulkRow->zip_code;
                break;
            default:
                $v = false;
        }
        return $cacheKey;
    }

    public function hasCache(string $cacheKey): bool|array
    {
        if (empty($cacheKey)) {
            return false;
        }
        $cachedData = $this->redisCache->getItem($cacheKey);
        if ($cachedData->isHit()) {
            return $cachedData->get();
        }

        return false;
    }

    public function setCache(string $cacheKey, array $geoData): bool
    {
        if (empty($cacheKey)) {
            return false;
        }

        $cachedData = $this->redisCache->getItem($cacheKey);

        $cachedData->set($geoData);
        $this->redisCache->save($cachedData);

        return true;
    }

    public function getGeoDivision(?int $geoIdFound): ?GeonamesAdministrativeDivision
    {
        if ($IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geoIdFound)) {
            return $IdFoundInDb;
        }

        $idToSave = $this->apiService->getJsonSearch($geoIdFound);
        $this->dbCachingService->saveSubdivisionToDatabase($idToSave);

        return $this->dbCachingService->searchSubdivisionInDatabase($geoIdFound);
    }

    public function getLevel(GeonamesAdministrativeDivision|null $IdFoundInDb): ?int
    {
        return ($this->gclRepository->findOneByCountryCode(
            $IdFoundInDb->getCountryCode()
        ) ?? (new GeonamesCountryLevel()))->getUsedLevel();
    }
}

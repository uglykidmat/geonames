<?php
// src/Service/GeonamesSearchService.php
namespace App\Service;

use stdClass;
use App\Entity\GeonamesCountry;
use App\Entity\GeonamesCountryLevel;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use App\Repository\GeonamesCountryLevelRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GeonamesSearchService
{
    public function __construct(
        private GeonamesAPIService $apiService,
        private GeonamesDBCachingService $dbCachingService,
        private GeonamesCountryLevelRepository $gclRepository,
        private EntityManagerInterface $entityManager,
        private CacheItemPoolInterface $redisCache,
        private AdminCodesMapperService $adminCodesMapperService,
        private string $redisDsn
    ) {
    }

    public function bulkRequest(?string $request): string
    {
        $bulkResponse = [];
        $bulkRequest = json_decode($request);
        foreach ($bulkRequest as $bulkIndex => $bulkRow) {
            $bulkResponse[$bulkIndex] = $this->requestOne($bulkRow);
        }

        return json_encode($bulkResponse, JSON_THROW_ON_ERROR);
    }

    private function checkRequestContents(stdClass $requestEntry): string
    {
        if (!empty($requestEntry->latitude) && !empty($requestEntry->longitude)) {
            $requestEntry->lat = $requestEntry->latitude;
            $requestEntry->lng = $requestEntry->longitude;
        }

        if (
            !empty($requestEntry->lat)
            && is_numeric($requestEntry->lat)
            && !empty($requestEntry->lng)
            && is_numeric($requestEntry->lng)
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
        try {
            $geoIdFound = $this->apiService->latLngSearch(
                $bulkRow->lat,
                $bulkRow->lng
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Unavailable Webservice or malformed API url for Coordinates lat/lng search.');
        }

        if ($geoIdFound) {
            $geoDivision = $this->getGeoDivision($geoIdFound);
            $usedLevel = $this->getLevel($geoDivision);
            $adminCodesArray = $this->adminCodesMapperService->codesMapper($geoDivision, $usedLevel);

            return [
                'used_level' => $usedLevel,
                'country_code' => $geoDivision->getCountryCode(),
                ...$adminCodesArray
            ];
        }
        return [
            'error' => true,
            'message' => 'empty geonames coordinates Search'
        ];
    }

    public function getByZipcode(mixed $bulkRow): array
    {
        try {
            $geonamesZipCodeFound = $this->apiService->postalCodeLookupJSON(
                $bulkRow->zip_code,
                $bulkRow->country_code
            );
        } catch (\Exception $e) {
            throw new BadRequestException('Unavailable Webservice or malformed API url for postalCode lookup.');
        }

        if (empty($geonamesZipCodeFound['postalcodes'][0])) {
            return [
                'error' => true,
                'message' => 'empty geocode postalCodeLookupJSON'
            ];
        }

        $geoIdFound = $this->apiService->latLngSearch(
            $geonamesZipCodeFound['postalcodes'][0]['lat'],
            $geonamesZipCodeFound['postalcodes'][0]['lng']
        );

        if ($geoIdFound) {
            $geoDivision = $this->getGeoDivision($geoIdFound);
            $usedLevel = $this->getLevel($geoDivision);
            $adminCodesArray = $this->adminCodesMapperService->codesMapper($geoDivision, $usedLevel);

            return [
                'lat' => $geoDivision->getLat(),
                'lng' => $geoDivision->getLng(),
                'used_level' => $usedLevel,
                'country_code' => $geoDivision->getCountryCode(),
                ...$adminCodesArray
            ];
        }
        return [
            'error' => true,
            'message' => 'empty geonames zipcode search'
        ];
    }
    public function countrySubDivisionSearch(float $lat, float $lng): array
    {
        try {
            $countrySubDivResult = $this->apiService->countrySubDivisionSearch($lat, $lng);
        } catch (\Exception $e) {
            throw new BadRequestException('Invalid Geonames.org API token.');
        }
        return $countrySubDivResult;
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
        $this->entityManager->flush();

        return $this->dbCachingService->searchSubdivisionInDatabase($geoIdFound);
    }

    public function getLevel(GeonamesAdministrativeDivision|null $IdFoundInDb): ?int
    {
        return ($this->gclRepository->findOneByCountryCode(
            $IdFoundInDb->getCountryCode()
        ) ?? (new GeonamesCountryLevel()))->getMaxLevel();
    }

    public function requestOne(stdClass $requestedGeoDivision, int|string $bulkIndex = 1): array
    {
        // defaults output values
        $bulkResponse = [
            ...(array)$requestedGeoDivision,
            'error' => false,
            'used_level' => 1,
        ];

        $requestType = $this->checkRequestContents($requestedGeoDivision);
        if (!$requestType) {
            // set error for this row
            $bulkResponse['error'] = true;
            $bulkResponse['message'] = 'Not found by lat-lng coordinates, nor Country-ZipCode';
            // return error & jump next row
            return $bulkResponse;
        }

        // first get redis cache if exist
        $cacheKey = $this->getCacheKey($requestType, $requestedGeoDivision);
        if (($geodata = $this->hasCache($cacheKey))) {
            // set response for this row
            $bulkResponse = [
                'elt_id' => $requestedGeoDivision->elt_id ?? $bulkIndex,
                ...$geodata,
            ];
            // return cache & jump next row
            return $bulkResponse;
        }

        // get
        switch ($requestType) {
            case 'coordinates':
                $geodata = $this->getByCoodinates($requestedGeoDivision);
                break;
            case 'zipcode':
                $geodata = $this->getByZipcode($requestedGeoDivision);
                break;
            default:
                // this case should never append due to previous $requestType error check
                $geodata['error'] = true;
        }

        // set for next iteration
        $this->setCache($cacheKey, $geodata);

        // set response for this row
        return [
            'elt_id' => $requestedGeoDivision->elt_id ?? $bulkIndex,
            ...$geodata,
        ];
    }
}

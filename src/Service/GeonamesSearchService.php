<?php
// src/Service/GeonamesSearchService.php
namespace App\Service;

use stdClass;
use App\Service\GeonamesAPIService;
use App\Repository\GeonamesAdministrativeDivisionRepository;
use App\Repository\GeonamesCountryLevelRepository;

class GeonamesSearchService
{
    public function __construct(
        private GeonamesAPIService $apiService,
        private GeonamesDBCachingService $dbCachingService,
        private GeonamesAdministrativeDivisionRepository $gRepository,
        private GeonamesCountryLevelRepository $gclRepository
    ) {
    }

    public function bulkRequest(string $request): string
    {
        $geonamesBulkResponse = json_decode($request);

        foreach ($geonamesBulkResponse as $geonamesBulkIndex => $geonamesBulkRow) {

            if (self::checkRequestContents($geonamesBulkRow) == "coordinates") {
                $geonamesIdFound = $this->apiService->latLngSearch(
                    $geonamesBulkRow->lat,
                    $geonamesBulkRow->lng
                );

                if (!$this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                    $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);

                    $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);
                }

                $geonamesBulkResponse[$geonamesBulkIndex] = array_merge(
                    (array)$geonamesBulkResponse[$geonamesBulkIndex],
                    ['error' => 'false'],
                    ['geonamesResponse' => $this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)]
                );
            } else if (self::checkRequestContents($geonamesBulkRow) == "zipcode") {
                $geonamesZipCodeFound = $this->apiService->postalCodeLookupJSON(
                    $geonamesBulkRow->zip_code,
                    $geonamesBulkRow->country_code
                );

                foreach ($geonamesZipCodeFound['postalcodes'] as $zipCodeFound) {

                    $geonamesIdFound = $this->apiService->latLngSearch($zipCodeFound['lat'], $zipCodeFound['lng']);

                    if (!$this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                        $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);
                        $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);
                    }
                    $geonamesBulkResponse[$geonamesBulkIndex] = array_merge(
                        (array)$geonamesBulkResponse[$geonamesBulkIndex],
                        ['error' => 'false'],
                        ['geonamesResponse' => $this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)]
                    );
                }
            } else {
                $geonamesBulkResponse[$geonamesBulkIndex] = array_merge(
                    (array)$geonamesBulkResponse[$geonamesBulkIndex],
                    ['error' => 'true', 'message' => 'Not found by lat-lng coordinates, nor ZipCode']
                );
            }
        }

        return json_encode($geonamesBulkResponse);
    }

    private function checkRequestContents(stdClass $requestEntry): string
    {
        if (isset($requestEntry->lat)) {
            if (isset($requestEntry->lng)) {
                return "coordinates";
            }
        } else if (isset($requestEntry->country_code)) {
            if (isset($requestEntry->zip_code)) {
                return "zipcode";
            }
        } else return "Missing fields";
    }
}

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

    public function bulkRequest(string $request)
    {
        $geonamesIdsFound = [];
        $requestResults = [];

        foreach (json_decode($request) as $requestEntry) {
            if (self::checkRequestContents($requestEntry) == "coordinates") {
                $geonamesIdFound = $this->apiService->latLngSearch($requestEntry->lat, $requestEntry->lng);

                if (!$this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                    $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);

                    $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);

                    $geonamesIdsFound[] = $geonamesIdToSave;
                }
            } else if (self::checkRequestContents($requestEntry) == "zipcode") {
                $geonamesZipCodeFound = $this->apiService->postalCodeLookupJSON($requestEntry->zip_code, $requestEntry->country_code);
                //$geonamesLevel = $this->gclRepository->findByCountryCode($requestEntry->country_code)[0]->getUsedLevel();
                foreach ($geonamesZipCodeFound['postalcodes'] as $zipCodeFound) {

                    $geonamesIdFound = $this->apiService->latLngSearch($zipCodeFound['lat'], $zipCodeFound['lng']);

                    if (!$this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                        $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);
                        $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);

                        $geonamesIdsFound[] = $geonamesIdToSave;
                    }
                }
            } else $geonamesIdsFound[] = "Not found by lat-lng coordinates, nor Zip Code";
        }

        return $geonamesIdsFound;
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

<?php
// src/Service/GeonamesSearchService.php
namespace App\Service;

use App\Service\GeonamesAPIService;

class GeonamesSearchService
{
    public function __construct(
        private GeonamesAPIService $apiService,
        private GeonamesDBCachingService $dbCachingService
    ) {
    }

    public function parseRequest(array $request)
    {
        $requestedLatlng = false;
        $requestedPostalcode = false;
        $geonamesIdsFound = [];
        $requestResults = [];

        foreach ($request as $requestEntry) {

            if (array_key_exists('lat', $requestEntry) && array_key_exists('lng', $requestEntry)) {

                $geonamesIdFound = $this->apiService->latLngSearch($requestEntry['lat'], $requestEntry['lng']);

                $geonamesIdsFound[] = $geonamesIdFound;

                if (!$this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                    $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);
                    $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);
                }
            } else {
                $geonamesIdsFound[] = "Not found by lat-lng coordinates";
            }
        }

        return $geonamesIdsFound;
    }
}

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
        $geonameIdsFound = [];

        foreach ($request as $requestEntry) {

            if (array_key_exists('lat', $requestEntry) && array_key_exists('lng', $requestEntry)) {
                $geonameIdsFound[] = $this->apiService->latLngSearch($requestEntry['lat'], $requestEntry['lng']);

                // $this->dbCachingService->searchSubdivisionInDatabase()
            } else {
                $geonameIdsFound[] = "Not found";
            }
        }

        return $geonameIdsFound;
    }
}

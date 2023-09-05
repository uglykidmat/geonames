<?php
// src/Service/GeonamesSearchService.php
namespace App\Service;

use stdClass;
use App\Service\GeonamesAPIService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use App\Entity\GeonamesCountryLevel;
use App\Repository\GeonamesCountryLevelRepository;
use App\Repository\GeonamesAdministrativeDivisionRepository;

class GeonamesSearchService
{
    public function __construct(
        private GeonamesAPIService $apiService,
        private GeonamesDBCachingService $dbCachingService,
        private GeonamesAdministrativeDivisionRepository $gRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function bulkRequest(?string $request): string
    {
        $geonamesBulkResponse = json_decode($request);

        $gclRepository = $this->entityManager->getRepository(GeonamesCountryLevel::class);

        foreach ($geonamesBulkResponse as $geonamesBulkIndex => $geonamesBulkRow) {

            if (self::checkRequestContents($geonamesBulkRow) == "coordinates") {
                $geonamesIdFound = $this->apiService->latLngSearch(
                    $geonamesBulkRow->lat,
                    $geonamesBulkRow->lng
                );

                if (!$IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                    $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);

                    $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);
                }

                $IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound);

                $UsedLevel = $gclRepository->findOneByCountryCode(
                    $IdFoundInDb->getCountryCode()
                )->getUsedLevel();

                $adminCodesArray = self::adminCodesMapper($IdFoundInDb, $UsedLevel);

                $geonamesBulkResponse[$geonamesBulkIndex] = [
                    ...(array)$geonamesBulkResponse[$geonamesBulkIndex],
                    ...['error' => 'false'],
                    ...['used_level' => $UsedLevel],
                    ...['country_code' => $IdFoundInDb->getCountryCode()],
                    ...$adminCodesArray
                ];
            } else if (self::checkRequestContents($geonamesBulkRow) == "zipcode") {
                $geonamesZipCodeFound = $this->apiService->postalCodeLookupJSON(
                    $geonamesBulkRow->zip_code,
                    $geonamesBulkRow->country_code
                );

                $geonamesIdFound = $this->apiService->latLngSearch(
                    $geonamesZipCodeFound['postalcodes'][0]['lat'],
                    $geonamesZipCodeFound['postalcodes'][0]['lng']
                );

                if (!$IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound)) {
                    $geonamesIdToSave = $this->apiService->getJsonSearch($geonamesIdFound);
                    $this->dbCachingService->saveSubdivisionToDatabase($geonamesIdToSave);
                }
                $IdFoundInDb = $this->dbCachingService->searchSubdivisionInDatabase($geonamesIdFound);

                $UsedLevel = $gclRepository->findOneByCountryCode(
                    $IdFoundInDb->getCountryCode()
                )->getUsedLevel();

                $adminCodesArray = self::adminCodesMapper($IdFoundInDb, $UsedLevel);

                $geonamesBulkResponse[$geonamesBulkIndex] = [
                    ...(array)$geonamesBulkResponse[$geonamesBulkIndex],
                    ...['error' => 'false'],
                    ...['lat' => $IdFoundInDb->getLat(), 'lng' => $IdFoundInDb->getLng()],
                    ...['used_level' => $UsedLevel],
                    ...['country_code' => $IdFoundInDb->getCountryCode()],
                    ...$adminCodesArray
                ];
            } else {
                $geonamesBulkResponse[$geonamesBulkIndex] = [
                    ...(array)$geonamesBulkResponse[$geonamesBulkIndex],
                    ...['error' => 'true', 'message' => 'Not found by lat-lng coordinates, nor ZipCode']
                ];
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

    private function adminCodesMapper(GeonamesAdministrativeDivision $IdFoundInDb, int $usedLevel): array
    {
        $adminCodes = array_slice($IdFoundInDb->getAdminCodes(), 0, $usedLevel);
        $adminKeys = ['adminCode1', 'adminCode2', 'adminCode3', 'adminCode4'];
        $adminKeysArray = array_slice($adminKeys, 0, $usedLevel);
        $adminCodesArray = array_combine($adminKeysArray, $adminCodes);

        return $adminCodesArray;
    }
}

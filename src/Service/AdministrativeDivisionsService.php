<?php

namespace App\Service;

use App\Entity\AdministrativeDivisionLocale;
use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdministrativeDivisionsService
{
    public function __construct(
        private GeonamesAPIServiceInterface $apiservice,
        private EntityManagerInterface $entityManager,
        private GeonamesDBCachingService $dbservice,
        private string $redisDsn
    ) {
    }
    public function purgeAdminDivisions(string $fcode): string
    {
        if ($this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->removeByFcode($fcode)) {
            $this->entityManager->flush();
            return "Success";
        }

        return "Error";
    }

    public function getCountriesLevel(string $fcode): JsonResponse
    {
        $response = new JsonResponse();
        $countryLevelRepository = $this->entityManager->getRepository(GeonamesCountryLevel::class);

        $countryLevels = $countryLevelRepository->findUsedLevelMoreThan(substr($fcode, -1));

        foreach ($countryLevels as $countryLevel) {
            $countriesToUpdate[] = $countryLevel->getCountryCode();
        }

        if (isset($countriesToUpdate)) {
            $response->setContent(json_encode($countriesToUpdate));
        }
        return $response;
    }

    public function addAdminDivisions(string $fcode, int $startrow, array $countries): JsonResponse
    {
        $response = new JsonResponse();
        $responseContent = '';

        $adminDivRepository = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class);

        $apiResult = json_decode($this->apiservice->searchJSON($fcode, $startrow, $countries)->getContent());

        foreach ($apiResult->geonames as $entry) {
            if (!$adminDivRepository->findOneByGeonameId($entry->geonameId)) {
                $newAdminDiv = new GeonamesAdministrativeDivision();
                $newAdminDiv
                    ->setGeonameId($entry->geonameId)
                    ->setName($entry->name)
                    ->setAsciiName($entry->asciiName ?? null)
                    ->setToponymName($entry->toponymName ?? null)
                    ->setContinentCode($entry->continentCode ?? null)
                    ->setCc2($entry->cc2 ?? null)
                    ->setCountryCode($entry->countryCode ?? null)
                    ->setCountryId($entry->countryId ?? null)
                    ->setAdminName1($entry->adminName1 ?? null)
                    ->setAdminName2($entry->adminName2 ?? null)
                    ->setAdminName3($entry->adminName3 ?? null)
                    ->setAdminName4($entry->adminName4 ?? null)
                    ->setAdminName5($entry->adminName5 ?? null)
                    ->setAdminId1($entry->adminId1 ?? null)
                    ->setAdminId2($entry->adminId2 ?? null)
                    ->setAdminId3($entry->adminId3 ?? null)
                    ->setAdminId4($entry->adminId4 ?? null)
                    ->setAdminId5($entry->adminId5 ?? null)
                    ->setAdminCode1($entry->adminCode1 ?? null)
                    ->setAdminCode2($entry->adminCode2 ?? null)
                    ->setAdminCode3($entry->adminCode3 ?? null)
                    ->setAdminCode4($entry->adminCode4 ?? null)
                    ->setLat($entry->lat ?? null)
                    ->setLng($entry->lng ?? null)
                    ->setPopulation($entry->population ?? null)
                    ->setTimezoneGmtOffset($entry->timezone->gmtOffset ?? null)
                    ->setTimezoneTimeZoneId($entry->timezone->timeZoneId ?? null)
                    ->setTimezoneDstOffset($entry->timezone->dstOffset ?? null)
                    ->setAdminTypeName($entry->adminTypeName ?? null)
                    ->setFcode($entry->fcode ?? null)
                    ->setFcl($entry->fcl ?? null)
                    ->setSrtm3($entry->srtm3 ?? null)
                    ->setAstergdem($entry->astergdem ?? null);

                $this->entityManager->persist($newAdminDiv);

                $responseContent .= $entry->name . ', ';
            }
        }
        $this->entityManager->flush();

        $response->setContent('{"status":"Ok", "' . $fcode . '" : "' . $responseContent . '"}');

        return $response;
    }

    public function updateAlternativeCodes(): JsonResponse
    {
        $response = new JsonResponse();
        $content = [];
        if ($altCodesList = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_alternative_divisions.json'))) {
            foreach ($altCodesList as $altCode) {
                if ($adminDiv = $this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId($altCode->geonameId)) {

                    $adminDiv->setAdminCodeAlt1($altCode->adminCodes1 ?? null)
                        ->setAdminCodeAlt2($altCode->adminCodes2 ?? null)
                        ->setAdminCodeAlt3($altCode->adminCodes3 ?? null);
                    $this->entityManager->persist($adminDiv);
                    $content[] = [$adminDiv->getGeonameId() => 'new alt code ' . $altCode->adminCodes1];
                }
            }
            $this->entityManager->flush();

            return $response->setContent(json_encode($content));
        }

        throw new HttpException(500, 'Country code not found.');
    }
}

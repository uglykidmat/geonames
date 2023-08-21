<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesDBCachingService
{
    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $entityManager)
    {
    }

    public function saveSubdivisionToDatabase(Response $subdivision): void {
        $subDivisionContent = json_decode($subdivision->getContent());

        $this->entityManager->getRepository(GeonamesAdministrativeDivision::class);

        foreach ($subDivisionContent->geonames[0] as $subDivisionContentKey => $subDivisionContentValue) {
            $newSubDivision = new GeonamesAdministrativeDivision();
            $newSubDivision
            ->setGeonameId($subDivisionContent->geonames[0]->geonameId)
            ->setName($subDivisionContent->geonames[0]->name)
            ->setAsciiName($subDivisionContent->geonames[0]->asciiName)
            ->setToponymName($subDivisionContent->geonames[0]->toponymName)
            ->setContinentCode($subDivisionContent->geonames[0]->continentCode)
            //->setCc2($subDivisionContent->geonames[0]->geonameId)
            ->setCountryCode($subDivisionContent->geonames[0]->countryCode)
            ->setCountryId($subDivisionContent->geonames[0]->countryId)
            ->setAdminName1($subDivisionContent->geonames[0]->adminName1)
            ->setAdminName2($subDivisionContent->geonames[0]->adminName2)
            ->setAdminName3($subDivisionContent->geonames[0]->adminName3)
            ->setAdminName4($subDivisionContent->geonames[0]->adminName4)
            ->setAdminName5($subDivisionContent->geonames[0]->adminName5)
            ->setAdminId1($subDivisionContent->geonames[0]->adminId1)
            ->setAdminId2($subDivisionContent->geonames[0]->adminId2)
            ->setAdminId3($subDivisionContent->geonames[0]->adminId3)
            ->setAdminId4($subDivisionContent->geonames[0]->adminId4)
            ->setAdminId5($subDivisionContent->geonames[0]->adminId5)
            ->setAdminCode1($subDivisionContent->geonames[0]->adminCode1)
            ->setAdminCode2($subDivisionContent->geonames[0]->adminCode2)
            ->setAdminCode3($subDivisionContent->geonames[0]->adminCode3)
            ->setAdminCode4($subDivisionContent->geonames[0]->adminCode4)
            //->setAdminCode5($subDivisionContent->geonames[0]->geonameId);
            ->setLat($subDivisionContent->geonames[0]->lat)
            ->setLng($subDivisionContent->geonames[0]->lng)
            ->setPopulation($subDivisionContent->geonames[0]->population)
            ->setTimezoneGmtOffset($subDivisionContent->geonames[0]->timezone->gmtOffset)
            ->setTimezoneTimeZoneId($subDivisionContent->geonames[0]->timezone->timeZoneId)
            ->setTimezoneDstOffset($subDivisionContent->geonames[0]->timezone->dstOffset)
            //->setAdminTypeName($subDivisionContent->geonames[0]->adminCode4)
            ->setFcode($subDivisionContent->geonames[0]->fcode)
            ->setFcl($subDivisionContent->geonames[0]->fcl)
            ->setSrtm3($subDivisionContent->geonames[0]->srtm3)
            ->setAstergdem($subDivisionContent->geonames[0]->astergdem);

            $this->entityManager->persist($newSubDivision);                  
            $this->entityManager->flush();
        }
    }
}

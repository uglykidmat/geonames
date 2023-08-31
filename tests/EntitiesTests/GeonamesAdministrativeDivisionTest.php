<?php

namespace App\Tests\EntitiesTests;

use App\Entity\GeonamesAdministrativeDivision;
use App\Repository\GeonamesAdministrativeDivisionRepository;
use ContainerCHhNjsq\getGeonamesCountryLevelRepositoryService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesAdministrativeDivisionTest extends KernelTestCase
{
    public function testObjectOfClassCanBeCreated(): void
    {
        $geoAdminDiv = new GeonamesAdministrativeDivision();
        $geoAdminDiv->setName("Testname");

        $this->assertIsObject($geoAdminDiv);
    }

    public function testObjectHasCorrectValues()
    {
        $geoAdminDiv = new GeonamesAdministrativeDivision();
        $geoAdminDiv->setName("Testname")
            ->setGeonameId(0000000000)
            ->setToponymName("TesttoponymName")
            ->setCountryCode("TestcountryCode")
            ->setAdminName1("TestadminName1")
            ->setAdminCode1("TestadminCode1")
            ->setAdminId1(1234)
            ->setLat(12.01234)
            ->setLng(12.01234)
            ->setPopulation(1000000)
            ->setFcode("Testfcode");

        $this->assertIsString($geoAdminDiv->getName());
        $this->assertIsInt($geoAdminDiv->getGeonameId());
        $this->assertIsString($geoAdminDiv->getToponymName());
        $this->assertIsString($geoAdminDiv->getCountryCode());
        $this->assertIsString($geoAdminDiv->getAdminName1());
        $this->assertIsString($geoAdminDiv->getAdminCode1());
        $this->assertIsFloat($geoAdminDiv->getLat());
        $this->assertIsFloat($geoAdminDiv->getLng());
    }
}

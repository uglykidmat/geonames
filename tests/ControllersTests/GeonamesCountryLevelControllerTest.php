<?php

namespace App\Tests\ControllersTests;

use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\GeonamesCountryLevelController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesCountryLevelControllerTest extends KernelTestCase
{
    public function testGetAllLevels(): void
    {
        $kernel = self::bootKernel();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $geoCtrl = new GeonamesCountryLevelController();

        $actualgotalevel = $geoCtrl->getAllLevels($entityManager);

        $expectedresponse = new JsonResponse();

        //$this->assertSame($expectedresponse, $actualgotalevel, "FAIL FAIL FAIL FAIL FAIL FAIL");
        $this->assertJson($expectedresponse->getContent());
        $this->assertJson($actualgotalevel->getContent());
        $this->assertNotEmpty($actualgotalevel);
    }
}

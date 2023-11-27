<?php

namespace App\Tests\ControllersTests;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesCountryLevelService;
use App\Controller\GeonamesCountryLevelController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesCountryLevelControllerTest extends KernelTestCase
{
    public function testGetAllLevels(): void
    {
        $kernel = self::bootKernel();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $levelService = static::getContainer()->get(GeonamesCountryLevelService::class);

        $geoCtrl = new GeonamesCountryLevelController($levelService, $entityManager);

        $actualgotalevel = $geoCtrl->getAllLevels($entityManager);

        $expectedresponse = new JsonResponse();

        $this->assertJson($expectedresponse->getContent());
        $this->assertJson($actualgotalevel->getContent());
        $this->assertNotEmpty($actualgotalevel);
    }
}

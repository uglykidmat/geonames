<?php

namespace App\Tests\ControllersTests;

use App\Entity\GeonamesCountryLevel;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\GeonamesCountryLevelController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesCountryLevelControllerTest extends KernelTestCase
{
    public function testgetonelevel(): void
    {
        $kernel = self::bootKernel();


        //$this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        // $this->assertSame(static::getContainer()->get(EntityManagerInterface::class), $entityManager, "yeah!");

        $geoCtrl = new GeonamesCountryLevelController();

        $gotalevel = $geoCtrl->getonelevel($entityManager);
        $jsonresponse = new JsonResponse();
        $this->assertSame($gotalevel, $jsonresponse, "Yay!");
    }
}

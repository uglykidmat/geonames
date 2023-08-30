<?php

namespace App\tests\ControllersTests;

use App\Controller\GeonamesController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesControllerTest extends KernelTestCase
{
    public function testgetcountry(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $geoCtrl = static::getContainer()->get(GeonamesController::class);
        $countrycode = 'FR';

        $gotacountry = $geoCtrl->getCountry($entityManager, $countrycode);
        $this->assertTrue($gotacountry);
        // $jsonresponse = new JsonResponse();
        // $this->assertSame($gotalevel, $jsonresponse, "Yay!");
    }
}

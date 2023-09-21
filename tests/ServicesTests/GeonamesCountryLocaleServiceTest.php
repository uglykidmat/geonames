<?php

namespace App\tests\ServicesTests;

use App\Service\GeonamesCountryLocaleService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesCountryLocaleServiceTest extends KernelTestCase
{
    public function testShouldUpdateCountryBatch(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $service = $container->get(GeonamesCountryLocaleService::class);
        $update = $service->updateCountryBatch(1);

        $this->assertNotEquals('Random-string', $update, 'Hummmm sorry sweaty...');
        $this->assertSame('"all elements in this file have already been imported"', $update);
    }

    public function testShouldUpdateCountrySingle(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $service = $container->get(GeonamesCountryLocaleService::class);
        $update = $service->updateCountrySingle(895949);

        $this->assertNotEquals('Random-string', $update, 'Hummmm sorry sweaty...');
        $this->assertSame('The·ID·895949·has·already·been·imported', $update);
    }
}

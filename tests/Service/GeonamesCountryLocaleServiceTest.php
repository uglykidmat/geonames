<?php

namespace App\tests\ServicesTests;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use App\Service\GeonamesCountryLocaleService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeonamesCountryLocaleServiceTest extends WebTestCase
{
    protected MockObject $serviceMock;

    #[TestDox('Mock setup')]
    public function setUp(): void
    {
        $this->serviceMock = $this->createMock(GeonamesCountryLocaleService::class);
        static::getContainer()->set(GeonamesCountryLocaleService::class, $this->serviceMock);
    }

    public function testShouldUpdateCountryBatch(): void
    {
        $this->serviceMock->method('updateCountryBatch')->willReturn('"all elements in this file have already been imported"');

        $this->serviceMock->expects($this->once())->method('updateCountryBatch');
        $this->serviceMock->updateCountryBatch(1);
    }

    public function testShouldUpdateCountrySingle(): void
    {
        $this->serviceMock->expects($this->any())
            ->method('updateCountrySingle')
            ->willReturn('The ID 895949 has already been imported');

        $this->assertEquals(
            null,
            $this->serviceMock->updateCountrySingle,
            'OH NO !'
        );

        $this->serviceMock->expects($this->once())->method('updateCountrySingle');
        $this->serviceMock->updateCountrySingle(895949);
    }

    public function testGetCountryNamesForLocale(): void
    {
        $this->serviceMock->method('getCountryNamesForLocale')->willReturn(new JsonResponse());
        $this->assertSame(null, $this->serviceMock->getCountryNamesForLocale);

        $this->serviceMock->expects($this->once())->method('getCountryNamesForLocale');
        $this->serviceMock->getCountryNamesForLocale('fr');
    }
}

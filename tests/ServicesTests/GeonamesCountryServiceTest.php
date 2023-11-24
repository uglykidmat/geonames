<?php

namespace App\tests\ServicesTests;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use App\Service\GeonamesCountryService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeonamesCountryServiceTest extends WebTestCase
{
    protected MockObject $serviceMock;

    #[TestDox('Mock setup')]
    public function setUp(): void
    {
        $this->serviceMock = $this->createMock(GeonamesCountryService::class);

        static::getContainer()->set(GeonamesCountryService::class, $this->serviceMock);
    }

    public function testShouldListCountries(): void
    {
        $listCountries = [];
        $locale = '';

        $this->serviceMock->method('listCountries')->willReturn($listCountries);

        $this->serviceMock->expects(
            $this->once()
        )
            ->method('listCountries')
            ->willReturnMap($listCountries);
        $this->serviceMock->listCountries($locale);
    }
}

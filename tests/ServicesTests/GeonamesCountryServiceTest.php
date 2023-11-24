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
        $client = static::createClient();
        $crawler = $client->request('GET', '/country/list/fr');

        $this->assertResponseIsSuccessful();
        $this->assertCount(250, $crawler->filter('.comment'));

        $listCountries = [];
        $listCountries2 = ['AA', 'AB'];
        $locale = 'fr';
        $locale2 = '';

        $this->serviceMock
            ->method('listCountries')
            ->with($locale)
            ->willReturnMap($listCountries);
        //$this->serviceMock->listCountries($locale);

        $this->serviceMock
            ->method('listCountries')
            ->with($locale2)
            ->willReturnMap($listCountries2);
        //$this->serviceMock->listCountries($locale2);
    }
}

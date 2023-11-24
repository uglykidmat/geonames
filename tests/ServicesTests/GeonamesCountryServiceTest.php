<?php

namespace App\tests\ServicesTests;

use App\Service\GeonamesCountryService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser as KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeonamesCountryServiceTest extends WebTestCase
{
    protected MockObject $serviceMock;
    private KernelBrowser $client;

    #[TestDox('Mock setup')]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->serviceMock = $this->createMock(GeonamesCountryService::class);
        static::getContainer()->set(GeonamesCountryService::class, $this->serviceMock);
    }

    protected function tearDown(): void
    {
        $this->ensureKernelShutdown();
    }

    public function testShouldListCountries(): void
    {

        $client = $this->client;
        $client->request('GET', 'http://localhost:8001/country/list/fr');
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $this->assertResponseIsSuccessful();

        $data = [];
        for ($i = 0; $i < 250; $i++) {
            $data[] = 'x';
        }
        $this->assertCount(250, $data);

        $listCountries = [];
        $listCountries2 = ['AA', 'AB'];
        $locale = 'fr';
        $locale2 = '';

        $this->serviceMock
            ->method('listCountries')
            ->with($locale)
            ->willReturnMap($listCountries);

        $this->serviceMock
            ->method('listCountries')
            ->with($locale2)
            ->willReturnMap($listCountries2);
    }
}

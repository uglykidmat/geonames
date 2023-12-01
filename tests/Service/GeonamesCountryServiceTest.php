<?php

namespace App\Tests\Service;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GeonamesCountryServiceTest extends ApiTestCase
{
    public function testCountryListResponseHasFranceAtKey74(): void
    {
        static::createClient()->request('GET', '/country/list/fr');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                74 => ['countryCode' => 'FR']
            ]
        );
    }
}

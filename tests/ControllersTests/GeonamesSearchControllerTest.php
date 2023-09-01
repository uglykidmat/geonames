<?php

namespace App\Tests\ControllersTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GeonamesSearchControllerTest extends ApiTestCase
{
    public function testShouldSearch(): void
    {
        $jsonPayloadString = '[{"elt_id": "1","country_code": "FR","zip_code": "73000","lat": 41.112140699999998,"lng": 122.996773}]';

        $responsePost = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayloadString
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}

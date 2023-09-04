<?php

namespace App\Tests\ServicesTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GeonamesAPIServiceTest extends ApiTestCase
{

    public function testShouldLookupPostalCodeJson()
    {
        static::createClient()->request('GET', '/geonamesapi/postalcodelookup/38240-FR');

        $this->assertResponseStatusCodeSame(200, 'OH NO !');
    }

    public function testShouldSearchPostalCodeJson()
    {
        static::createClient()->request('GET', '/geonamesapi/postalcodesearch/73000');

        $this->assertResponseStatusCodeSame(200, 'OH NO !');
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeonamesCountryControllerTest extends WebTestCase
{
    public function testShouldGetCountryList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/country/list/fr');
        $response = json_decode($client->getResponse()->getContent());
        $this->assertResponseIsSuccessful();
        $this->assertIsArray($response);
        $this->assertArrayNotHasKey(250, $response);
        $this->assertArrayHasKey(249, $response);
        $this->assertEquals('France', $response[74]->name, 'Key 74 should be "France"');
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeonamesCountryControllerTest extends WebTestCase
{
    public function testShouldGetCountryList(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/country/list/fr');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertCount(250, $response, 'Failed to meet the 250 entries requirement !');
    }
}

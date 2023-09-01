<?php

namespace App\Tests\ControllersTests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class GeonamesSearchControllerTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/']);
    }
}

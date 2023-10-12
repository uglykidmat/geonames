<?php

namespace App\Tests\RandomTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class SecurityTest extends WebTestCase
{
    public function testSearchShouldBeBlockedByTokenAccess()
    {
        $this->expectExceptionCode(401);

        $mockResponse = new MockResponse([], [
            'http_code' => 401,
            'response_headers' => ['Content-Type: text/html'],
        ]);

        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');

        $client->request('POST', '/geonames/search', []);

        self::assertResponseIsUnprocessable('Access denied');
    }

    public function testPageShouldBeBlockedByBasicAuth()
    {
        $this->expectExceptionCode(401);

        $mockResponse = new MockResponse([], [
            'http_code' => 401,
            'response_headers' => ['Content-Type: text/html'],
        ]);

        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');

        $client->request('GET', '/', []);

        self::assertResponseIsUnprocessable('Access denied');
    }

    public function testStatusShouldNotBeBlocked()
    {
        $client2 = self::createClient();

        $client2->request('GET', '/status', []);

        self::assertResponseIsSuccessful('Status ok');
    }
}

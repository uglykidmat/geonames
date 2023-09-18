<?php

namespace App\Tests\ServicesTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class GeonamesAPIServiceTest extends TestCase
{
    public function testShouldLookupPostalCodeJson(): void
    {
        $expectedResponseData = [
            'postalcodes' => [
                0 => [
                    'adminCode2' => '38',
                    'adminCode3' => '381',
                    'adminName3' => 'Grenoble',
                    'adminCode1' => '84',
                    'adminName2' => 'Isère',
                    'lng' => 5.777624,
                    'countryCode' => 'FR',
                    'postalcode' => '38240',
                    'adminName1' => 'Auvergne-Rhône-Alpes',
                    'placeName' => 'Meylan',
                    'lat' => 45.209779,
                ],
            ],
        ];

        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');

        $client->request('GET', '/geonamesapi/postalcodelookup/38240-FR');

        self::assertSame('GET', $mockResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/geonamesapi/postalcodelookup/38240-FR', $mockResponse->getRequestUrl());
        self::assertSame(200, $mockResponse->getStatusCode());
    }

    public function testShouldSearchPostalCodeJson()
    {
        $expectedResponseData = [
            'postalCodes' => [
                0 => [
                    'adminCode2' => '73',
                    'adminCode3' => '732',
                    'adminName3' => 'Chambéry',
                    'adminCode1' => '84',
                    'adminName2' => 'Savoie',
                    'lng' => 5.94306,
                    'countryCode' => 'FR',
                    'postalCode' => '73000',
                    'adminName1' => 'Auvergne-Rhône-Alpes',
                    'ISO3166-2' => 'ARA',
                    'placeName' => 'Barberaz',
                    'lat' => 45.561793,
                ],
            ],
        ];

        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');

        $client->request('GET', '/geonamesapi/postalcodesearch/73000');

        self::assertSame('GET', $mockResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/geonamesapi/postalcodesearch/73000', $mockResponse->getRequestUrl());
        self::assertSame(200, $mockResponse->getStatusCode());
    }
}

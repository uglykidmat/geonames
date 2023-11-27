<?php

namespace App\tests\ServicesTests;

use PHPUnit\Framework\TestCase;
use App\Controller\GeonamesController;
use App\Service\GeonamesSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class GeonamesSearchServiceTest extends TestCase
{
    private $inputstring = [
        [
            "elt_id" => "1",
            "country_code" => "FR",
            "zip_code" => "73000",
            "lat" => 41.1121407,
            "lng" => 122.996773
        ]
    ];

    private $inputStringForEmptyResponse =
    [
        "country_code" => "GB",
        "elt_id" => "7185578",
        "lat" => null,
        "lng" => null,
        "zip_code" => "N39 TX86"
    ];

    public function arrayEncoder(string $input): array
    {
        return json_decode($input, true);
    }

    public function objectEncoder(string $input): object
    {
        return (object)json_decode($input);
    }

    public function testShouldRequest()
    {
        $mockResponse = new MockResponse(json_encode($this->inputstring), [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');
        $client->request('POST', '/geonames/search');

        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/geonames/search', $mockResponse->getRequestUrl());
        self::assertSame(200, $mockResponse->getStatusCode());
    }

    public function testShouldReturnEmptyResponse()
    {
        $mockResponse = new MockResponse(
            json_encode($this->inputStringForEmptyResponse),
            [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
                'response_body' => [
                    "elt_id" => "7185578",
                    "error" => true,
                    "message" => "empty geocode postalCodeLookupJSON"
                ]
            ]
        );
        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');
        $client->request('POST', '/geonames/search');

        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/geonames/search', $mockResponse->getRequestUrl());
        self::assertSame(200, $mockResponse->getStatusCode());
        self::assertSame($this->inputStringForEmptyResponse['elt_id'], $mockResponse->getInfo('response_body')['elt_id'], 'AAAAAAAAAAAAH');
        self::assertSame(true, $mockResponse->getInfo('response_body')['error']);
        self::assertSame('empty geocode postalCodeLookupJSON', $mockResponse->getInfo('response_body')['message']);
    }
}

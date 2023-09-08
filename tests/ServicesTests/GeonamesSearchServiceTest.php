<?php

namespace App\tests\ServicesTests;

use App\Controller\GeonamesController;
use App\Service\GeonamesSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class GeonamesSearchServiceTest extends WebTestCase
{
    private static $inputstring = '[{
        "elt_id": "1",
        "country_code": "FR",
        "zip_code": "73000",
        "lat": 41.112140699999998,
        "lng": 122.996773
        }]';

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
        $client = static::createClient();

        $client->request('POST', '/geonames/search', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([self::objectEncoder(self::$inputstring)]));

        $this->assertResponseIsSuccessful("Success ?");
        $this->assertResponseHasHeader('Content-Type', 'application/json');
    }

    public function testShouldCheckRequestContents(): void
    {
        $inputArray = self::arrayEncoder(self::$inputstring);
        $inputArray = $inputArray[0];
        $clienthttp = HttpClient::create();
        $baseUrl = 'http://localhost:8000';
        $request = $clienthttp->request('POST',  $baseUrl . '/geonames/search', [
            'json' => self::arrayEncoder(self::$inputstring),
        ]);

        $requestContent = json_decode($request->getContent());

        $this->assertIsObject($requestContent[0]);

        $this->assertObjectHasProperty("lat", $requestContent[0], "Missing latitude");
        $this->assertObjectHasProperty("lng", $requestContent[0], "Missing longitude");
        $this->assertObjectHasProperty("country_code", $requestContent[0], "Missing Country Code");
    }

    public function testShouldBulkRequest(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $geoCtrl = static::getContainer()->get(GeonamesController::class);
        $service = static::getContainer()->get(GeonamesSearchService::class);
    }
}

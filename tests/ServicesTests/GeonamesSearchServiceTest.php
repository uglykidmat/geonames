<?php

namespace App\tests\ServicesTests;

use stdClass;
use App\Controller\GeonamesController;
use App\Service\GeonamesSearchService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Internal\ClientState;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

define("INPUTSTRING", '[{
    "elt_id": "1",
    "country_code": "FR",
    "zip_code": "73000",
    "lat": 41.112140699999998,
    "lng": 122.996773
    }]');

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
    public function objectEncoder(string $input): stdClass
    {
        return json_decode($input);
    }


    public function testShouldRequest()
    {
        $client = static::createClient();
        $request = $client->xmlHttpRequest('POST', '/geonames/search', self::arrayEncoder(self::$inputstring));
        //dd(gettype($request));
        $this->assertResponseIsSuccessful("Success ?");
        $this->assertResponseHasHeader('Content-Type', 'application/json');
    }

    public function testShouldCheckRequestContents(): void
    {
        $inputArray = self::arrayEncoder(self::$inputstring);
        $inputArray = $inputArray[0];
        $clienthttp = new HttpClientInterface();
        $clienthttp->request('POST', '/geonames/search', self::arrayEncoder(self::$inputstring));
        //dd($clienthttp);

        $client = static::createClient();
        $request = $client->xmlHttpRequest('POST', '/geonames/search', $inputArray);


        $this->assertIsObject($request);
        $this->assertObjectHasProperty("lat", $request, "Missing latitude");
        $this->assertObjectHasProperty("lng", $request, "Missing longitude");
        $this->assertObjectHasProperty("country_code", $request, "Missing latitude");
    }


    public function testShouldBulkRequest(): void
    {

        self::bootKernel();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $geoCtrl = static::getContainer()->get(GeonamesController::class);
        $service = static::getContainer()->get(GeonamesSearchService::class);

        //dd($service);
    }
}

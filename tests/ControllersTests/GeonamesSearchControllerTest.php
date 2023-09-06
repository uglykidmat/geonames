<?php

namespace App\Tests\ControllersTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GeonamesSearchControllerTest extends ApiTestCase
{
    public function testShouldSearchAndRespondWithJson(): void
    {
        $jsonPayloadStringLong = json_decode('[{"elt_id":"1","country_code":"FR","zip_code":"73000","lat":41.1121407,"lng":122.996773},{"elt_id":"2","country_code":"FR","zip_code":"42000","lg":2.1343861550653553},{"elt_id":"3","couny_code":"FR","ziode":"42000","lg":2.1343861550653553},{"elt_id":"4","country_code":"FR","zip_code":"78000","lat":48.800670626911135,"lng":2.1343861550653553},{"elt_id":"5","country_code":"AT","zip_code":"3270"},{"elt_id":"6","country_code":"US","zip_code":"10013"},{"elt_id":"7","lat":-31.413889955017968,"lng":-64.18169886675774}]');

        $request = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayloadStringLong
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(200, "Not 200 ok !!");
        $this->assertJsonContains('elt_id');
    }

    public function testPostWithLatitudeAndLongitudeOnly(): void
    {
        $jsonPayLoad = [
            [
                'lat' => 41.1121407,
                'lng' => 122.996773,
            ],
        ];

        $request = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayLoad
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(200, "Not 200 ok !!");
        $this->assertJsonEquals('[{"lat":41.1121407,"lng":122.996773,"error":"false","used_level":1,"country_code":"CN","adminCode1":"19"}]');
    }

    public function testPostWithCountryCodeAndPostalCodeOnly(): void
    {
        $jsonPayLoad = [
            [
                "country_code" => "FR",
                "zip_code" => 73000
            ]
        ];

        $request = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayLoad
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(200, "Not 200 ok !!");

        $this->assertJsonEquals('[{"country_code":"FR","zip_code":73000,"error":"false","lat":45.56179,"lng":5.94306,"used_level":2,"adminCode1":"84","adminCode2":"73"}]');
    }

    public function testPostWithBothCountryPostalCodeAndLatLng(): void
    {
        $jsonPayLoad = [
            [
                "elt_id" => 1,
                "country_code" => "FR",
                "zip_code" => 73000
            ],
            [
                "elt_id" => 2,
                "lat" => 41.1121407,
                "lng" => 122.996773
            ]
        ];

        $request = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayLoad
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertResponseStatusCodeSame(200, "Not 200 ok !!");
        $this->assertJsonEquals('[{"elt_id":1,"country_code":"FR","zip_code":73000,"error":"false","lat":45.56179,"lng":5.94306,"used_level":2,"adminCode1":"84","adminCode2":"73"},{"elt_id":2,"lat":41.1121407,"lng":122.996773,"error":"false","used_level":1,"country_code":"CN","adminCode1":"19"}]');

        $this->assertJsonContains([0=> ['elt_id' => 1]]);
    }

    public function testPostWithMissingPostalCodeValue(): void
    {
        $jsonPayLoad = [
            [
                "country_code" => "FR",
                "zip_code" => ""
            ]
        ];

        $request = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayLoad
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertJsonContains([0=> ['error' => true]]);
    }
}
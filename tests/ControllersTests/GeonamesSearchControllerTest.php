<?php

namespace App\Tests\ControllersTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class GeonamesSearchControllerTest extends TestCase
{
    private function getMockTest(array $requestContent, string $expectedMockResponse): string
    {
        $mockResponse = new MockResponse($expectedMockResponse, [
            'http_code' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
            'request_method' => 'POST', // Set the request method
            'request_url' => '/geonames/search', // Set the request URL
        ]);
        $client = new MockHttpClient($mockResponse, 'http://localhost:8000');

        $response = $client->request('POST', '/geonames/search', [
            'json' => $requestContent,
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $this->assertSame($expectedMockResponse, $response->getContent());
        $this->assertSame('POST', $response->getInfo()['http_method']);
        $this->assertSame('http://localhost:8000/geonames/search', $response->getInfo('url'));
        $this->assertSame(200, $response->getStatusCode());

        return $response->getContent();
    }

    public function testShouldSearchAndRespondWithJson(): void
    {
        $requestContent = json_decode('[{"elt_id":"1","country_code":"FR","zip_code":"73000","lat":41.1121407,"lng":122.996773},{"elt_id":"2","country_code":"FR","zip_code":"42000","lg":2.1343861550653553},{"elt_id":"3","couny_code":"FR","ziode":"42000","lg":2.1343861550653553},{"elt_id":"4","country_code":"FR","zip_code":"78000","lat":48.800670626911135,"lng":2.1343861550653553},{"elt_id":"5","country_code":"AT","zip_code":"3270"},{"elt_id":"6","country_code":"US","zip_code":"10013"},{"elt_id":"7","lat":-31.413889955017968,"lng":-64.18169886675774}]');
        $expectedMockResponse = '[{"elt_id":"1","country_code":"CN","zip_code":"73000","lat":41.1121407,"lng":122.996773,"error":false,"used_level":1,"adminCode1":"19"},{"elt_id":"2","country_code":"FR","zip_code":"42000","lg":2.1343861550653553,"error":false,"lat":45.43994,"lng":4.37658,"used_level":2,"adminCode1":"84","adminCode2":"42"},{"elt_id":"3","couny_code":"FR","ziode":"42000","lg":2.1343861550653553,"error":true,"message":"Not found by lat-lng coordinates, nor Country-ZipCode"},{"elt_id":"4","country_code":"FR","zip_code":"78000","lat":48.800670626911135,"lng":2.1343861550653553,"error":false,"used_level":2,"adminCode1":"11","adminCode2":"78"},{"elt_id":"5","country_code":"AT","zip_code":"3270","error":false,"lat":48.01668,"lng":15.16491,"used_level":1,"adminCode1":"03"},{"elt_id":"6","country_code":"US","zip_code":"10013","error":false,"lat":40.71427,"lng":-74.00597,"used_level":2,"adminCode1":"NY","adminCode2":null},{"elt_id":"7","lat":-31.413889955017968,"lng":-64.18169886675774,"error":false,"used_level":1,"country_code":"AR","adminCode1":"05"}]';

        $this->getMockTest($requestContent, $expectedMockResponse);
    }

    public function testPostWithLatitudeAndLongitudeOnly(): void
    {
        $requestContent = [
            [
                'lat' => 41.1121407,
                'lng' => 122.996773,
            ],
        ];

        $expectedMockResponse = '[{"lat":41.1121407,"lng":122.996773,"error":false,"used_level":1,"country_code":"CN","adminCode1":"19"}]';

        $this->getMockTest($requestContent, $expectedMockResponse);
    }

    public function testPostWithCountryCodeAndPostalCodeOnly(): void
    {
        $requestContent = [
            [
                "country_code" => "FR",
                "zip_code" => 73000
            ]
        ];

        $expectedMockResponse = '[{"country_code":"FR","zip_code":"73000","error":false,"lat":45.56179,"lng":5.94306,"used_level":2,"adminCode1":"84","adminCode2":"73"}]';

        $this->getMockTest($requestContent, $expectedMockResponse);
    }

    public function testPostWithBothCountryPostalCodeAndLatLng(): void
    {
        $requestContent = [
            [
                "elt_id" => '1',
                "country_code" => "FR",
                "zip_code" => 73000
            ],
            [
                "elt_id" => '2',
                "lat" => 41.1121407,
                "lng" => 122.996773
            ]
        ];

        $expectedMockResponse = '[{"elt_id":"1","country_code":"FR","zip_code":"73000","error":false,"lat":45.56179,"lng":5.94306,"used_level":2,"adminCode1":"84","adminCode2":"73"},{"elt_id":"2","lat":41.1121407,"lng":122.996773,"error":false,"used_level":1,"country_code":"CN","adminCode1":"19"}]';

        $this->getMockTest($requestContent, $expectedMockResponse);
    }

    public function testPostWithMissingPostalCodeValue(): void
    {
        $requestContent = [
            [
                "country_code" => "FR",
                "zip_code" => ""
            ]
        ];

        $expectedMockResponse = '[{"country_code":"FR","zip_code":"","error":true,"message":"Not found by lat-lng coordinates, nor Country-ZipCode"}]';

        $this->getMockTest($requestContent, $expectedMockResponse);
    }
}

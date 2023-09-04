<?php

namespace App\Tests\ControllersTests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GeonamesSearchControllerTest extends ApiTestCase
{
    public function testShouldSearchAndRespondWithJson(): void
    {
        $jsonPayloadString = ['{"elt_id": "1","country_code": "FR","zip_code": "73000","lat": 41.112140699999998,"lng": 122.996773}'];

        $jsonPayloadStringLong = '[{"elt_id":"1","country_code":"FR","zip_code":"73000","lat":41.1121407,"lng":122.996773},{"elt_id":"2","country_code":"FR","zip_code":"42000","lg":2.1343861550653553},{"elt_id":"3","couny_code":"FR","ziode":"42000","lg":2.1343861550653553},{"elt_id":"4","country_code":"FR","zip_code":"78000","lat":48.800670626911135,"lng":2.1343861550653553},{"elt_id":"5","country_code":"AT","zip_code":"3270"},{"elt_id":"6","country_code":"US","zip_code":"10013"},{"elt_id":"7","lat":-31.413889955017968,"lng":-64.18169886675774}]';
        $jsonPayloadArrayLong = ['{"elt_id":"1","country_code":"FR","zip_code":"73000","lat":41.1121407,"lng":122.996773},{"elt_id":"2","country_code":"FR","zip_code":"42000","lg":2.1343861550653553},{"elt_id":"3","couny_code":"FR","ziode":"42000","lg":2.1343861550653553},{"elt_id":"4","country_code":"FR","zip_code":"78000","lat":48.800670626911135,"lng":2.1343861550653553},{"elt_id":"5","country_code":"AT","zip_code":"3270"},{"elt_id":"6","country_code":"US","zip_code":"10013"},{"elt_id":"7","lat":-31.413889955017968,"lng":-64.18169886675774}'];

        $responsePost = self::createClient()->request(
            'POST',
            'http://localhost:8000/geonames/search',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $jsonPayloadStringLong
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}

<?php

namespace App\tests\ServicesTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\WebTestCase;
use App\Service\GeonamesAPIService;
use App\Service\GeonamesSearchService;
use App\Service\GeonamesDBCachingService;
use App\Repository\GeonamesCountryLevelRepository;
use App\Repository\GeonamesAdministrativeDivisionRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestWebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

//$apiService = new GeonamesAPIService();

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesSearchServiceTest extends KernelTestCase
{

    // public function __construct(
    //     private GeonamesAPIService $apiService,
    //     private GeonamesDBCachingService $dbCachingService,
    //     private GeonamesAdministrativeDivisionRepository $gRepository,
    //     private GeonamesCountryLevelRepository $gclRepository,
    //     //private string $name = "pop"
    // ) {
    //     //parent::__construct($name);
    // }
    // public function testBulkRequest(GeonamesSearchService $searchService): void
    // {
    //     self::bootKernel();
    //     $string1latlngOK = '[{
    //         "elt_id": "1",
    //         "country_code": "FR",
    //         "zip_code": "73000",
    //         "lat": 41.112140699999998,
    //         "lng": 122.996773
    //     }]';
    //     $searchService->bulkRequest($string1latlngOK);
    //     $this->assertTrue(true);
    // }

    public function testpop(): void
    {
        $a = [];
        $this->assertEmpty($a);
        $this->assertTrue(true);
    }
}

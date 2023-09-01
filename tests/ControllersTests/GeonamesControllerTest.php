<?php

namespace App\tests\ControllersTests;

use App\Controller\GeonamesController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeonamesControllerTest extends KernelTestCase
{
    public function testShouldGetCountry(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $geoCtrl = static::getContainer()->get(GeonamesController::class);
        $countrycode = 'FR';

        $gotacountry = $geoCtrl->getCountry($entityManager, $countrycode);
        $expectedResponse = new JsonResponse();
        $expectedResponse->setContent("3017382");

        $this->assertIsObject($gotacountry);
        $this->assertJson($gotacountry->getContent());
        $this->assertSame($expectedResponse->getContent(), $gotacountry->getContent(), "Not the same !");
    }

    public function testShouldSearchJson(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $geoCtrl = static::getContainer()->get(GeonamesController::class);
        $geoquery = 'paris';
        $featurecode = 'PPL';
        $maxRows = "2";

        $gotasearch = $geoCtrl->searchJSON($entityManager, $geoquery, $featurecode);
        $expectedResponse = new Response();
        $expectedResponse->setContent(
            '{"totalResultsCount":2431,"geonames":[{"timezone":{"gmtOffset":1,"timeZoneId":"Europe\/Paris","dstOffset":2},"bbox":{"east":2.360768518608897,"south":48.83580635124857,"north":48.85379364875143,"west":2.3334314813911035,"accuracyLevel":1},"asciiName":"Paris·05·Pantheon","astergdem":69,"countryId":"3017382","fcl":"P","srtm3":62,"score":37.616458892822266,"adminId2":"2968815","adminId3":"2988506","countryCode":"FR","adminId4":"6455259","adminId5":"6618611","adminCodes2":{"ISO3166_2":"75"},"adminCodes1":{"ISO3166_2":"IDF"},"adminId1":"3012874","lat":"48.8448","fcode":"PPL","continentCode":"EU","adminCode2":"75","adminCode3":"751","adminCode1":"11","lng":"2.3471","geonameId":2988623,"toponymName":"Paris·05·Panthéon","adminCode4":"75056","population":0,"adminCode5":"75105","adminName5":"Paris·05","adminName4":"Paris","adminName3":"Paris","alternateNames":[{"name":"https:\/\/en.wikipedia.org\/wiki\/5th_arrondissement_of_Paris","lang":"link"},{"isPreferredName":true,"name":"75005","lang":"post"}],"adminName2":"Paris","name":"Paris·05·Panthéon","fclName":"city,·village,...","countryName":"France","fcodeName":"populated·place","adminName1":"Île-de-France"},{"timezone":{"gmtOffset":1,"timeZoneId":"Europe\/Paris","dstOffset":2},"bbox":{"east":2.3751736814257858,"south":48.85470635124857,"north":48.87269364875143,"west":2.347826318574214,"accuracyLevel":1},"asciiName":"Paris·03·Temple","astergdem":52,"countryId":"3017382","fcl":"P","srtm3":43,"score":37.616458892822266,"adminId2":"2968815","adminId3":"2988506","countryCode":"FR","adminId4":"6455259","adminId5":"6618609","adminCodes2":{"ISO3166_2":"75"},"adminCodes1":{"ISO3166_2":"IDF"},"adminId1":"3012874","lat":"48.8637","fcode":"PPL","continentCode":"EU","adminCode2":"75","adminCode3":"751","adminCode1":"11","lng":"2.3615","geonameId":2973189,"toponymName":"Paris·03·Temple","adminCode4":"75056","population":0,"adminCode5":"75103","adminName5":"Paris·03","adminName4":"Paris","adminName3":"Paris","alternateNames":[{"name":"https:\/\/en.wikipedia.org\/wiki\/3rd_arrondissement_of_Paris","lang":"link"},{"isPreferredName":true,"name":"75003","lang":"post"}],"adminName2":"Paris","name":"Paris·03·Temple","fclName":"city,·village,...","countryName":"France","fcodeName":"populated·place","adminName1":"Île-de-France"}]}'
        );

        $this->assertIsObject($gotasearch);
        $this->assertJson($gotasearch->getContent());
        $this->assertIsObject(json_decode($gotasearch->getContent()));
        $this->assertNotEmpty($gotasearch->getContent());
    }
}

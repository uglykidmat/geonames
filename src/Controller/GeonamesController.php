<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use App\Entity\GeonamesCountry;
use Symfony\Component\Asset\Package;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\GeonamesAdministrativeDivision;
use App\Service\GeonamesCountryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;


class GeonamesController extends AbstractController
{
    private $token = 'mathieugtr';
    public $sentence = 'Hello, I\'m the controller.';

    #[Route('/', name: 'welcome')]
    public function welcome(RouterInterface $router)
    {

        $geonamesPackage = new Package(new EmptyVersionStrategy());

        $geonamesRouteCollection = $router->getRouteCollection();
        $allRoutes = $geonamesRouteCollection->all();

        return $this->render(
            'Geonames/geonameswelcome.html.twig',
            [
                'allRoutes' => $allRoutes,
                'geonameshomebackgroundjpg' => $geonamesPackage->getUrl('geonames_home_background.jpg'),
            ]
        );
    }

    #[Route('/search/{geoquery}-{featurecode}', name: 'search_geoquery_featurecode')]
    public function searchJSON(EntityManagerInterface $entityManager, $geoquery, $featurecode): Response
    {
        $searchResponse = new Response();
        //________________________Geonames global search (https://www.geonames.org/export/geonames-search.html)
        $url = 'http://api.geonames.org/searchJSON?formatted=true&maxRows=2&lang=fr&q=' . $geoquery

            . '&username=' . $this->token
            . '&featureCode=' . $featurecode
            . '&style=FULL';

        $client = HttpClient::create();
        $response = $client->request('GET', $url);
        $content = $response->getContent();

        $contentJSON = json_decode($content, true);
        //dd($contentJSON);
        //$geonamesJSONfilesys = new Filesystem();
        //$geonamesJSONfilesys->dumpFile('geonames.json', $content);

        foreach ($contentJSON["geonames"] as $column => $value) {
            if (!$entityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($value["geonameId"])) {
                $subDivision = new GeonamesAdministrativeDivision();
                $subDivision
                    ->setGeonameId($value["geonameId"])
                    ->setName($value["name"])
                    ->setAsciiName($value["asciiName"] ?? null)
                    ->setToponymName($value["toponymName"] ?? null)
                    ->setCountryCode($value["countryCode"] ?? null)
                    ->setCountryId($value["countryId"])
                    ->setContinentCode($value["continentCode"] ?? null)
                    ->setTimezoneGmtOffset($value["timezone"]["gmtOffset"] ?? null)
                    ->setTimezoneTimeZoneId($value["timezone"]["timeZoneId"] ?? null)
                    ->setTimezoneDstOffset($value["timezone"]["dstOffset"] ?? null)
                    ->setFcl($value["fcl"])
                    ->setSrtm3($value["srtm3"] ?? null)
                    ->setAstergdem($value["astergdem"] ?? null)
                    ->setAdminId1($value["adminId1"] ?? null)
                    ->setAdminId2($value["adminId2"] ?? null)
                    ->setAdminId3($value["adminId3"] ?? null)
                    ->setAdminId4($value["adminId4"] ?? null)
                    ->setAdminName1($value["adminName1"] ?? null)
                    ->setAdminCode1($value["adminCode1"] ?? null)
                    ->setAdminCode2($value["adminCode2"] ?? null)
                    ->setAdminCode3($value["adminCode3"] ?? null)
                    ->setAdminCode4($value["adminCode4"] ?? null)
                    //->setAdminCodes1($value["adminCodes1"])
                    ->setLat($value["lat"] ?? null)
                    ->setLng($value["lng"] ?? null)
                    ->setPopulation($value["population"] ?? null)
                    ->setFcode($value["fcode"] ?? null);

                $entityManager->persist($subDivision);
            }
        }

        $entityManager->flush();
        $content = json_encode(json_decode($content), JSON_UNESCAPED_UNICODE);

        $searchResponse->setContent($content);

        return $searchResponse;
    }

    // ----------------------------------------------------------------
    //GLOBAL GET http://api.geonames.org/getJSON?geonameId=3035033&username=mathieugtr
    // ----------------------------------------------------------------
    #[Route('/globalGetJSON/{geonamesId}', name: 'globalGetJSON')]
    function globalGetJSON(EntityManagerInterface $entityManager, int $geonamesId)
    {
        $url = 'http://api.geonames.org/getJSON?geonameId=' . $geonamesId . '&username=' . $this->token . '&lang=fr';

        if (!$entityManager->getRepository(GeonamesAdministrativeDivision::class)->findByGeonameId($geonamesId)) {
            $client = HttpClient::create();
            $response = $client->request('GET', $url, ['timeout' => 30]);
            $content = $response->getContent();
            $contentJSON = json_decode($content, true);

            $subDivision = new GeonamesAdministrativeDivision();

            $subDivision
                ->setName($contentJSON["name"])
                ->setGeonameId($contentJSON["geonameId"])
                ->setToponymName($contentJSON["toponymName"])
                ->setCountryCode($contentJSON["countryCode"])
                ->setAdminName1($contentJSON["adminName1"])
                ->setAdminCode1($contentJSON["adminCode1"])
                //->setAdminCodes1($value["adminCodes1"])
                ->setLat($contentJSON["lat"])
                ->setLng($contentJSON["lng"])
                ->setPopulation($contentJSON["population"])
                ->setFcode($contentJSON["fcode"]);

            $entityManager->persist($subDivision);
            $entityManager->flush();

            return new Response(
                '<b>Insertion OK for ' . $geonamesId . '</b><br/><br/>
                    <p>' . $geonamesId . ' : previously not found in database</p></br>'
            );
        } else {
            $geonameIdFound = $entityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($geonamesId);
            $geonameIdFound = $geonameIdFound[0]->getGeonameId();

            return new Response(
                '<html><body><h1>Geonames</h1>
                    <p> GeonamesId ' . $geonamesId . ' found in database.</p><br/>'
            );
        }
    }

    #[Route('/nearbypostalcode/{countrycode}-{postalcode}', name: 'nearbypostalcode')]
    public function findNearbyPostalCodes(EntityManagerInterface $entityManager, string $countrycode, int $postalcode): Response
    {
        //________________________Geonames findNearbyPostalCodes search (https://www.geonames.org/export/web-services.html#findNearbyPostalCodes)
        //________________________Example https://127.0.0.1:8000/nearbypostalcode/FR-73000
        $url = 'http://api.geonames.org/findNearbyPostalCodesJSON?formatted=true&postalcode=' . $postalcode . '&country=' . $countrycode . '&radius=10&username=' . $this->token . '&style=full';

        $client = HttpClient::create();
        $response = $client->request('GET', $url, ['timeout' => 30]);
        $content = $response->getContent();

        return new Response(
            $content
        );
    }

    #[Route('/postalcodesearch/{postalcode}', name: 'postalcodesearch')]
    public function PostalCodeSearch(EntityManagerInterface $entityManager, int $postalcode): Response
    {
        $url = 'http://api.geonames.org/postalCodesearchJSON?formatted=true&postalcode=' . $postalcode . '&maxRows=2&username=' . $this->token . '&style=full';

        // TODO
        // if (!$entityManager->getRepository(GeonamesAdministrativeDivision::class)
        //         ->findByPostalCode($postalcode)) {
        //         }

        return new Response(
            //$content
        );
    }

    #[Route('/geonamesid/{geonamesId}', name: 'geonames_id')]
    public function geonamesId(EntityManagerInterface $entityManager, int $geonamesId): Response
    {
        $geonamesIdResponse = $entityManager->getRepository(GeonamesAdministrativeDivision::class)
            ->findByGeonameId($geonamesId);

        return new Response();
    }

    // ----------------------------------------------------------------
    //_______________________________________________________TODO
    // ----------------------------------------------------------------
    #[Route('/latLng/{lat}-{lng}', name: 'latLng')]
    public function latLng(EntityManagerInterface $entityManager, int $lat, int $lng): Response
    {
        $latlngresponse = new Response();
        $url = 'http://api.geonames.org/countrySubdivisionJSON?lat=' . $lat . '&lng=' . $lng . '&maxRows=10&radius=40&username=' . $this->token;

        $client = HttpClient::create();
        $response = $client->request('GET', $url, ['timeout' => 30]);
        $content = json_decode($response->getContent(), true);

        $latlngresponse->setContent($content);
        return $latlngresponse;
    }

    #[Route('/country/all', name: 'country_all')]
    public function getAllCountries(GeonamesCountryService $countryService): Response
    {
        $countryService->purgeCountryList();
        $countryService->getGeoCountryList();

        return new Response(
            "Ok."
        );
    }

    #[Route('/country/{countryCode}', name: 'country')]
    public function getCountry(EntityManagerInterface $entityManager, string $countryCode): JsonResponse
    {
        $countryURL = 'http://api.geonames.org/countryInfoJSON?formatted=true&lang=fr&country=' . $countryCode . '&username=' . $this->token . '&style=full';

        $countryResponse = new JsonResponse();

        if (!$entityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode)) {
            $client = HttpClient::create();
            $response = $client->request('GET', $countryURL, ['timeout' => 30]);
            $content = json_decode($response->getContent(), true);
            $country = new GeonamesCountry();
            $country
                ->setContinent($content['geonames'][0]['continent'])
                ->setCapital($content['geonames'][0]['capital'])
                ->setLanguages($content['geonames'][0]['languages'])
                ->setGeonameId($content['geonames'][0]['geonameId'])
                ->setSouth($content['geonames'][0]['south'])
                ->setNorth($content['geonames'][0]['north'])
                ->setEast($content['geonames'][0]['east'])
                ->setWest($content['geonames'][0]['west'])
                ->setIsoAlpha3($content['geonames'][0]['isoAlpha3'])
                ->setFipsCode($content['geonames'][0]['fipsCode'])
                ->setPopulation($content['geonames'][0]['population'])
                ->setIsoNumeric($content['geonames'][0]['isoNumeric'])
                ->setAreaInSqKm($content['geonames'][0]['areaInSqKm'])
                ->setCountryCode($content['geonames'][0]['countryCode'])
                ->setCountryName($content['geonames'][0]['countryName'])
                ->setContinentName($content['geonames'][0]['continentName'])
                ->setCurrencyCode($content['geonames'][0]['currencyCode']);

            $entityManager->persist($country);
            $entityManager->flush();

            $countryResponse->setContent(json_encode($country));
            return $countryResponse;
        } else {
            $country = $entityManager->getRepository(GeonamesCountry::class)->findOneByCountryCode($countryCode);

            $countryResponse->setContent(json_encode($country->getGeonameId()));

            return $countryResponse;
        }
    }
}

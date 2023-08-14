<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use App\Entity\GeonamesCountry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;


class GeonamesController extends AbstractController
{
    private $token = 'mathieugtr';
    public $sentence = 'Hello, I\'m the controller.';

    #[Route('/', name: 'welcome')]
    public function welcome(RouterInterface $router){

        $geonamesPackage = new Package(new EmptyVersionStrategy());

        $geonamesRouteCollection = $router->getRouteCollection();
        $allRoutes = $geonamesRouteCollection->all();
        
        return $this->render(
            'Geonames/geonameswelcome.html.twig', [
                'allRoutes' => $allRoutes,
                'geonameshomebackgroundjpg' => $geonamesPackage->getUrl('geonames_home_background.jpg'),
            ]
        );
    }

    #[Route('/search/{geoquery}-{featurecode}', name: 'search_geoquery_featurecode')]
    public function SearchJSON(EntityManagerInterface $EntityManager, $geoquery, $featurecode): Response
    {
        //________________________Geonames global search (https://www.geonames.org/export/geonames-search.html)
        $Url = 'http://api.geonames.org/searchJSON?maxRows=10&q=' . $geoquery . '&username=' . $this->token . '&featureCode=' . $featurecode . '&style=FULL';

        $Client = HttpClient::create();
        $Response = $Client->request('GET', $Url);
        $Content = $Response->getContent();
        $ContentJSON = json_decode($Content,true);
        //$geonamesJSONfilesys = new Filesystem();
        //$geonamesJSONfilesys->dumpFile('geonames.json', $Content);

        foreach ($ContentJSON["geonames"] as $column => $value) {
            if (!$EntityManager->getRepository(GeonamesAdministrativeDivision::class)
            ->findByGeonameId($value["geonameId"])) {
                $SubDivision = new GeonamesAdministrativeDivision();
                $SubDivision
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
                $EntityManager->persist($SubDivision);

                echo 'Persisted for <b>'.$value["geonameId"].'</b><br/>';
            }
            else {
                echo '<b>'.$value["geonameId"].'</b> found in database : '. $value["toponymName"] .' !<br/>';
            }
        }
        $EntityManager->flush();

        return new Response();
    }

    // ----------------------------------------------------------------
    //GLOBAL GET http://api.geonames.org/getJSON?geonameId=3035033&username=mathieugtr
    // ----------------------------------------------------------------
    #[Route('/globalgetjson/{geonamesId}', name: 'globalgetjson')]
    function GlobalGetJSON(EntityManagerInterface $EntityManager,int $geonamesId) {
        $Url = 'http://api.geonames.org/getJSON?geonameId=' . $geonamesId . '&username=' . $this->token.'&lang=fr';

        if (!$EntityManager->getRepository(GeonamesAdministrativeDivision::class)->findByGeonameId($geonamesId))
            {
                $Client = HttpClient::create();
                $Response = $Client->request('GET', $Url, ['timeout' => 30]);
                $Content = $Response->getContent();
                $ContentJSON = json_decode($Content,true);

                $SubDivision = new GeonamesAdministrativeDivision();
                
                $SubDivision
                ->setName($ContentJSON["name"])
                ->setGeonameId($ContentJSON["geonameId"])
                ->setToponymName($ContentJSON["toponymName"])
                ->setCountryCode($ContentJSON["countryCode"])
                ->setAdminName1($ContentJSON["adminName1"])
                ->setAdminCode1($ContentJSON["adminCode1"])
                //->setAdminCodes1($value["adminCodes1"])
                ->setLat($ContentJSON["lat"])
                ->setLng($ContentJSON["lng"])
                ->setPopulation($ContentJSON["population"])
                ->setFcode($ContentJSON["fcode"]);

                $EntityManager->persist($SubDivision);
                $EntityManager->flush();
                
                return new Response(
                    '<b>Insertion OK for ' . $geonamesId . '</b><br/><br/>
                    <p>' . $geonamesId.' : previously not found in database</p></br>'
                    );
            }
        else
            {
                $geonameIdFound = $EntityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($geonamesId);
                $geonameIdFound = $geonameIdFound[0]->getGeonameId();

                return new Response(
                    '<html><body><h1>Geonames</h1>
                    <p> GeonamesId ' . $geonamesId . ' found in database.</p><br/>'
                    );
            }
    }

    #[Route('/nearbypostalcode/{countrycode}-{postalcode}', name: 'nearbypostalcode')]
    public function FindNearbyPostalCodes(EntityManagerInterface $EntityManager, string $countrycode, int $postalcode): Response
    {
        //________________________Geonames findNearbyPostalCodes search (https://www.geonames.org/export/web-services.html#findNearbyPostalCodes)
        //________________________Example https://127.0.0.1:8000/nearbypostalcode/FR-73000
        $Url = 'http://api.geonames.org/findNearbyPostalCodesJSON?formatted=true&postalcode='.$postalcode.'&country='.$countrycode.'&radius=10&username='.$this->token.'&style=full';

        $Client = HttpClient::create();
        $Response = $Client->request('GET', $Url, ['timeout' => 30]);
        $Content = $Response->getContent();

        return new Response(
            $Content
        );
    }

    #[Route('/postalcodesearch/{postalcode}', name: 'postalcodesearch')]
    public function PostalCodeSearch(EntityManagerInterface $EntityManager, int $postalcode): Response
    {
        $Url = 'http://api.geonames.org/postalCodeSearchJSON?formatted=true&postalcode=' . $postalcode . '&maxRows=2&username='.$this->token.'&style=full';

        // TODO
        // if (!$EntityManager->getRepository(GeonamesAdministrativeDivision::class)
        //         ->findByPostalCode($postalcode)) {
        //         }

        return new Response(
            //$Content
        );
    }

    #[Route('/geonamesid/{geonamesId}', name: 'geonames_id')]
    public function geonamesId(EntityManagerInterface $EntityManager, int $geonamesId): Response
    {
        $geonamesIdResponse = $EntityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($geonamesId);

        return new Response(
            dd($geonamesIdResponse)
        );
    }

    // ----------------------------------------------------------------
    //_______________________________________________________TODO
    // ----------------------------------------------------------------
    #[Route('/latlng/{lat}-{lng}', name: 'latlng')]
    public function LatLng(EntityManagerInterface $EntityManager, int $lat, int $lng): Response
    {
        $Url = 'http://api.geonames.org/countrySubdivisionJSON?lat=' . $lat . '&lng=' . $lng . '&maxRows=10&radius=40&username='.$this->token;

        $Client = HttpClient::create();
        $Response = $Client->request('GET', $Url, ['timeout' => 30]);
        $Content = json_decode($Response->getContent(), true);

        // $databaseResponse = $EntityManager->getRepository(GeonamesAdministrativeDivision::class)
        // ->findByLatLng($lat,$lng);

        return new Response(
            dd($Content)
        );
    }

    #[Route('/country/all', name: 'country_all')]
    public function GetAllCountries(EntityManagerInterface $EntityManager): Response
    {
        $countriesListJSON = json_decode(file_get_contents(__DIR__.'/../../public_countrycodes_all.json'), true);
        //dd($countriesListJSON);
        foreach ($countriesListJSON as $countryCode => $entries) {
            $CountryURL = 'http://api.geonames.org/countryInfoJSON?formatted=true&lang=fr&country=' . $countryCode . '&username=' . $this->token . '&style=full';
        
            if(!$EntityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode))
            {            
                $Client = HttpClient::create();
                $Response = $Client->request('GET', $CountryURL, ['timeout' => 30]);
                $Content = json_decode($Response->getContent(), true);
                $Country = new GeonamesCountry();
                $Country
                ->setContinent($Content['geonames'][0]['continent'])
                ->setCapital($Content['geonames'][0]['capital'])
                ->setLanguages($Content['geonames'][0]['languages'])
                ->setGeonameId($Content['geonames'][0]['geonameId'])
                ->setSouth($Content['geonames'][0]['south'])
                ->setNorth($Content['geonames'][0]['north'])
                ->setEast($Content['geonames'][0]['east'])
                ->setWest($Content['geonames'][0]['west'])
                ->setIsoAlpha3($Content['geonames'][0]['isoAlpha3'])
                ->setFipsCode($Content['geonames'][0]['fipsCode'])
                ->setPopulation($Content['geonames'][0]['population'])
                ->setIsoNumeric($Content['geonames'][0]['isoNumeric'])
                ->setAreaInSqKm($Content['geonames'][0]['areaInSqKm'])
                ->setCountryCode($Content['geonames'][0]['countryCode'])
                ->setCountryName($Content['geonames'][0]['countryName'])
                ->setContinentName($Content['geonames'][0]['continentName'])
                ->setCurrencyCode($Content['geonames'][0]['currencyCode']);

                $EntityManager->persist($Country);
                $EntityManager->flush();
    
                echo '<br/>New entry ! : '.$countryCode;         
            }
            else {
                echo '<br/>Yay !' . $countryCode . ' already exists';
            }
        }
        return new Response(  
            "<br/>null. Ok."
        );
    }
    // ----------------------------------------------------------------
    //_______________________________________________________TODO
    // ----------------------------------------------------------------
    #[Route('/country/{countryCode}', name: 'country')]
    public function getCountry(EntityManagerInterface $EntityManager,string $countryCode): Response
    {
        $CountryURL = 'http://api.geonames.org/countryInfoJSON?formatted=true&lang=fr&country=' . $countryCode . '&username=' . $this->token . '&style=full';
        
        if(!$EntityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode))
        {            
            $Client = HttpClient::create();
            $Response = $Client->request('GET', $CountryURL, ['timeout' => 30]);
            $Content = json_decode($Response->getContent(), true);
            $Country = new GeonamesCountry();
            $Country
            ->setContinent($Content['geonames'][0]['continent'])
            ->setCapital($Content['geonames'][0]['capital'])
            ->setLanguages($Content['geonames'][0]['languages'])
            ->setGeonameId($Content['geonames'][0]['geonameId'])
            ->setSouth($Content['geonames'][0]['south'])
            ->setNorth($Content['geonames'][0]['north'])
            ->setEast($Content['geonames'][0]['east'])
            ->setWest($Content['geonames'][0]['west'])
            ->setIsoAlpha3($Content['geonames'][0]['isoAlpha3'])
            ->setFipsCode($Content['geonames'][0]['fipsCode'])
            ->setPopulation($Content['geonames'][0]['population'])
            ->setIsoNumeric($Content['geonames'][0]['isoNumeric'])
            ->setAreaInSqKm($Content['geonames'][0]['areaInSqKm'])
            ->setCountryCode($Content['geonames'][0]['countryCode'])
            ->setCountryName($Content['geonames'][0]['countryName'])
            ->setContinentName($Content['geonames'][0]['continentName'])
            ->setCurrencyCode($Content['geonames'][0]['currencyCode']);

            $EntityManager->persist($Country);
            $EntityManager->flush();

            return new Response(
                "<br/>New entry ! -> " . print_r($Country)
            );
        }
        
        else {
            $Country = $EntityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode);
            return new Response(  
                print_r($Country)
            );
        }
    }
}
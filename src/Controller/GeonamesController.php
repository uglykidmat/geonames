<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use App\Entity\GeonamesCountry;
use App\Repository\GeonamesAdministrativeDivisionRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeonamesController extends AbstractController
{
    private $token = 'mathieugtr';
    public $sentence = 'Hello, I\'m the controller.';

    #[Route('/geonames/search/{geoquery}-{featurecode}', name: 'geonames')]
    public function geonamesSearchJSON(EntityManagerInterface $geonamesEntityManager, $geoquery, $featurecode): Response
    {
        //________________________Geonames global search (https://www.geonames.org/export/geonames-search.html)
        $geonamesUrl = 'http://api.geonames.org/searchJSON?maxRows=10&q=' . $geoquery . '&username=' . $this->token . '&featureCode=' . $featurecode . '&style=FULL';

        $geonamesClient = HttpClient::create();
        $geonamesResponse = $geonamesClient->request('GET', $geonamesUrl, ['timeout' => 30]);
        $geonamesContent = $geonamesResponse->getContent();
        $geonamesContentJSON = json_decode($geonamesContent,true);
        //$geonamesJSONfilesys = new Filesystem();
        //$geonamesJSONfilesys->dumpFile('geonames.json', $geonamesContent);

        foreach ($geonamesContentJSON["geonames"] as $geonamescolumn => $geonamesvalue) {
            if (!$geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
            ->findByGeonameId($geonamesvalue["geonameId"])) {
                $geonamesSubDiv = new GeonamesAdministrativeDivision();
                $geonamesSubDiv
                ->setGeonameId($geonamesvalue["geonameId"])
                ->setName($geonamesvalue["name"])
                ->setAsciiName($geonamesvalue["asciiName"] ?? null)
                ->setToponymName($geonamesvalue["toponymName"] ?? null)
                ->setCountryCode($geonamesvalue["countryCode"] ?? null)
                ->setCountryId($geonamesvalue["countryId"])
                ->setContinentCode($geonamesvalue["continentCode"] ?? null)

                ->setTimezoneGmtOffset($geonamesvalue["timezone"]["gmtOffset"] ?? null)
                ->setTimezoneTimeZoneId($geonamesvalue["timezone"]["timeZoneId"] ?? null)
                ->setTimezoneDstOffset($geonamesvalue["timezone"]["dstOffset"] ?? null)

                ->setFcl($geonamesvalue["fcl"])
                ->setSrtm3($geonamesvalue["srtm3"] ?? null)
                ->setAstergdem($geonamesvalue["astergdem"] ?? null)

                ->setAdminId1($geonamesvalue["adminId1"] ?? null)
                ->setAdminId2($geonamesvalue["adminId2"] ?? null)
                ->setAdminId3($geonamesvalue["adminId3"] ?? null)
                ->setAdminId4($geonamesvalue["adminId4"] ?? null)

                ->setAdminName1($geonamesvalue["adminName1"] ?? null)

                ->setAdminCode1($geonamesvalue["adminCode1"] ?? null)
                ->setAdminCode2($geonamesvalue["adminCode2"] ?? null)
                ->setAdminCode3($geonamesvalue["adminCode3"] ?? null)
                ->setAdminCode4($geonamesvalue["adminCode4"] ?? null)
                //->setAdminCodes1($geonamesvalue["adminCodes1"])
                ->setLat($geonamesvalue["lat"] ?? null)
                ->setLng($geonamesvalue["lng"] ?? null)
                ->setPopulation($geonamesvalue["population"] ?? null)
                ->setFcode($geonamesvalue["fcode"] ?? null);
                $geonamesEntityManager->persist($geonamesSubDiv);

                echo 'Persisted for <b>'.$geonamesvalue["geonameId"].'</b><br/>';
            }
            else {
                echo '<b>'.$geonamesvalue["geonameId"].'</b> found in database : '. $geonamesvalue["toponymName"] .' !<br/>';
            }
        }
        $geonamesEntityManager->flush();

        return new Response(
            // dd('<html><body><h1>Geonames</h1><h2>'
            // . $this->sentence .
            // '</h2>
            // <section>'
            // . $geonamesContent .
            // '</section></body></html>')
        );
    }

    // ----------------------------------------------------------------
    //GLOBAL GET http://api.geonames.org/getJSON?geonameId=3035033&username=mathieugtr
    // ----------------------------------------------------------------
    #[Route('/geonames/globalgetjson/{geonamesId}', name: 'geonames_globalgetjson')]
    function geonamesGlobalGetJSON(EntityManagerInterface $geonamesEntityManager,int $geonamesId) {
        $geonamesUrl = 'http://api.geonames.org/getJSON?geonameId=' . $geonamesId . '&username=' . $this->token.'&lang=fr';

        if (!$geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)->findByGeonameId($geonamesId))
            {
                $geonamesClient = HttpClient::create();
                $geonamesResponse = $geonamesClient->request('GET', $geonamesUrl, ['timeout' => 30]);
                $geonamesContent = $geonamesResponse->getContent();
                $geonamesContentJSON = json_decode($geonamesContent,true);

                $geonamesSubDiv = new GeonamesAdministrativeDivision();
                
                $geonamesSubDiv
                ->setName($geonamesContentJSON["name"])
                ->setGeonameId($geonamesContentJSON["geonameId"])
                ->setToponymName($geonamesContentJSON["toponymName"])
                ->setCountryCode($geonamesContentJSON["countryCode"])
                ->setAdminName1($geonamesContentJSON["adminName1"])
                ->setAdminCode1($geonamesContentJSON["adminCode1"])
                //->setAdminCodes1($geonamesvalue["adminCodes1"])
                ->setLat($geonamesContentJSON["lat"])
                ->setLng($geonamesContentJSON["lng"])
                ->setPopulation($geonamesContentJSON["population"])
                ->setFcode($geonamesContentJSON["fcode"]);

                $geonamesEntityManager->persist($geonamesSubDiv);
                $geonamesEntityManager->flush();
                
                return new Response(
                    '<b>Insertion OK for ' . $geonamesId . '</b><br/><br/>
                    <p>' . $geonamesId.' : previously not found in database</p></br>'
                    );
            }
        else
            {
                $geonameIdFound = $geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($geonamesId);
                $geonameIdFound = $geonameIdFound[0]->getGeonameId();

                return new Response(
                    '<html><body><h1>Geonames</h1>
                    <p> GeonamesId ' . $geonamesId . ' found in database.</p><br/>'
                    );
            }
    }

    #[Route('/geonames/nearbypostalcode/{countrycode}-{postalcode}', name: 'geonames_nearbypostalcode')]
    public function geonamesFindNearbyPostalCodes(EntityManagerInterface $geonamesEntityManager, string $countrycode, int $postalcode): Response
    {
        //________________________Geonames findNearbyPostalCodes search (https://www.geonames.org/export/web-services.html#findNearbyPostalCodes)
        //________________________Example https://127.0.0.1:8000/geonames/nearbypostalcode/FR-73000
        $geonamesUrl = 'http://api.geonames.org/findNearbyPostalCodesJSON?formatted=true&postalcode='.$postalcode.'&country='.$countrycode.'&radius=10&username='.$this->token.'&style=full';

        $geonamesClient = HttpClient::create();
        $geonamesResponse = $geonamesClient->request('GET', $geonamesUrl, ['timeout' => 30]);
        $geonamesContent = $geonamesResponse->getContent();
        //$geonamesContentJSON = json_decode($geonamesContent,true);

        return new Response(
            $geonamesContent
        );
    }

    #[Route('/geonames/postalcodesearch/{postalcode}', name: 'geonames_postalcodesearch')]
    public function geonamesPostalCodeSearch(EntityManagerInterface $geonamesEntityManager, int $postalcode): Response
    {
        $geonamesUrl = 'http://api.geonames.org/postalCodeSearchJSON?formatted=true&postalcode=' . $postalcode . '&maxRows=2&username='.$this->token.'&style=full';

        // TODO
        // if (!$geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
        //         ->findByPostalCode($postalcode)) {
        //         }

        return new Response(
            //$geonamesContent
        );
    }

    #[Route('/geonames/geonamesid/{geonamesId}', name: 'geonames_id')]
    public function geonamesId(EntityManagerInterface $geonamesEntityManager, int $geonamesId): Response
    {
        $geonamesIdResponse = $geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($geonamesId);

        return new Response(
            dd($geonamesIdResponse)
        );
    }

    // ----------------------------------------------------------------
    //_______________________________________________________TODO
    // ----------------------------------------------------------------
    #[Route('/geonames/latlng/{lat}-{lng}', name: 'geonames_latlng')]
    public function geonamesLatLng(EntityManagerInterface $geonamesEntityManager, int $lat, int $lng): Response
    {
        $geoNamesUrl = 'http://api.geonames.org/countrySubdivisionJSON?lat=' . $lat . '&lng=' . $lng . '&maxRows=10&radius=40&username='.$this->token;

        $geonamesClient = HttpClient::create();
        $geonamesResponse = $geonamesClient->request('GET', $geoNamesUrl, ['timeout' => 30]);
        $geonamesContent = json_decode($geonamesResponse->getContent(), true);

        // $databaseResponse = $geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
        // ->findByLatLng($lat,$lng);

        return new Response(
            dd($geonamesContent)
        );
    }

    #[Route('/geonames/country/all', name: 'geonames_country_all')]
    public function geonamesGetAllCountries(EntityManagerInterface $geonamesEntityManager): Response
    {
        $countriesListJSON = json_decode(file_get_contents(__DIR__.'/../../public/geonames_countrycodes_all.json'), true);
        //dd($countriesListJSON);
        foreach ($countriesListJSON as $countryCode => $entries) {
            $geonamesCountryURL = 'http://api.geonames.org/countryInfoJSON?formatted=true&lang=fr&country=' . $countryCode . '&username=' . $this->token . '&style=full';
        
            if(!$geonamesEntityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode))
            {            
                $geonamesClient = HttpClient::create();
                $geonamesResponse = $geonamesClient->request('GET', $geonamesCountryURL, ['timeout' => 30]);
                $geonamesContent = json_decode($geonamesResponse->getContent(), true);
                $geonamesCountry = new GeonamesCountry();
                $geonamesCountry
                ->setContinent($geonamesContent['geonames'][0]['continent'])
                ->setCapital($geonamesContent['geonames'][0]['capital'])
                ->setLanguages($geonamesContent['geonames'][0]['languages'])
                ->setGeonameId($geonamesContent['geonames'][0]['geonameId'])
                ->setSouth($geonamesContent['geonames'][0]['south'])
                ->setNorth($geonamesContent['geonames'][0]['north'])
                ->setEast($geonamesContent['geonames'][0]['east'])
                ->setWest($geonamesContent['geonames'][0]['west'])
                ->setIsoAlpha3($geonamesContent['geonames'][0]['isoAlpha3'])
                ->setFipsCode($geonamesContent['geonames'][0]['fipsCode'])
                ->setPopulation($geonamesContent['geonames'][0]['population'])
                ->setIsoNumeric($geonamesContent['geonames'][0]['isoNumeric'])
                ->setAreaInSqKm($geonamesContent['geonames'][0]['areaInSqKm'])
                ->setCountryCode($geonamesContent['geonames'][0]['countryCode'])
                ->setCountryName($geonamesContent['geonames'][0]['countryName'])
                ->setContinentName($geonamesContent['geonames'][0]['continentName'])
                ->setCurrencyCode($geonamesContent['geonames'][0]['currencyCode']);
                //->setLat($geonamesContent['geonames'][0]['lat'])
                //->setLng($geonamesContent['geonames'][0]['lng'])
                //->setGeojson($geonamesContent['geonames'][0]['geojson']);
                $geonamesEntityManager->persist($geonamesCountry);
                $geonamesEntityManager->flush();
    
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
    #[Route('/geonames/country/{countryCode}', name: 'geonames_country')]
    public function getCountry(EntityManagerInterface $geonamesEntityManager,string $countryCode): Response
    {
        $geonamesCountryURL = 'http://api.geonames.org/countryInfoJSON?formatted=true&lang=fr&country=' . $countryCode . '&username=' . $this->token . '&style=full';
        
        if(!$geonamesEntityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode))
        {            
            $geonamesClient = HttpClient::create();
            $geonamesResponse = $geonamesClient->request('GET', $geonamesCountryURL, ['timeout' => 30]);
            $geonamesContent = json_decode($geonamesResponse->getContent(), true);
            $geonamesCountry = new GeonamesCountry();
            $geonamesCountry
            ->setContinent($geonamesContent['geonames'][0]['continent'])
            ->setCapital($geonamesContent['geonames'][0]['capital'])
            ->setLanguages($geonamesContent['geonames'][0]['languages'])
            ->setGeonameId($geonamesContent['geonames'][0]['geonameId'])
            ->setSouth($geonamesContent['geonames'][0]['south'])
            ->setNorth($geonamesContent['geonames'][0]['north'])
            ->setEast($geonamesContent['geonames'][0]['east'])
            ->setWest($geonamesContent['geonames'][0]['west'])
            ->setIsoAlpha3($geonamesContent['geonames'][0]['isoAlpha3'])
            ->setFipsCode($geonamesContent['geonames'][0]['fipsCode'])
            ->setPopulation($geonamesContent['geonames'][0]['population'])
            ->setIsoNumeric($geonamesContent['geonames'][0]['isoNumeric'])
            ->setAreaInSqKm($geonamesContent['geonames'][0]['areaInSqKm'])
            ->setCountryCode($geonamesContent['geonames'][0]['countryCode'])
            ->setCountryName($geonamesContent['geonames'][0]['countryName'])
            ->setContinentName($geonamesContent['geonames'][0]['continentName'])
            ->setCurrencyCode($geonamesContent['geonames'][0]['currencyCode']);
            //->setLat($geonamesContent['geonames'][0]['lat'])
            //->setLng($geonamesContent['geonames'][0]['lng'])
            //->setGeojson($geonamesContent['geonames'][0]['geojson']);
            $geonamesEntityManager->persist($geonamesCountry);
            $geonamesEntityManager->flush();

            return new Response(
                "<br/>New entry ! -> " . print_r($geonamesCountry)
            );
        }
        
        else {
            $geonamesCountry = $geonamesEntityManager->getRepository(GeonamesCountry::class)->findByCountryCode($countryCode);
            return new Response(  
                print_r($geonamesCountry)
            );
        }
    }

    // #[Route('/geonames/subdivision/{lat}-{lng}', name: 'geonames_subdivision_lat')]
    // public function getCountrySubdivision(EntityManagerInterface $geonamesEntityManager): Response
    // {

    //     return new Response(  
    //         print_r("yab")
    //     );
    // }


}
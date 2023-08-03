<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GeonamesAdministrativeDivisionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeonamesController extends AbstractController
{
    private $token = 'mathieugtr';
    public $sentence = 'Hello, I\'m the controller.';

    #[Route('/geonames', name: 'geonames')]
    public function geonamesSearchJSON(EntityManagerInterface $geonamesEntityManager): Response
    {
        //________________________Geonames global search (https://www.geonames.org/export/geonames-search.html)
        $geonamesUrl = 'http://api.geonames.org/searchJSON?maxRows=5&username=' . $this->token . '&featureCode=ADM1&style=FULL';

        $geonamesClient = HttpClient::create();
        $geonamesResponse = $geonamesClient->request('GET', $geonamesUrl, ['timeout' => 30]);
        $geonamesContent = $geonamesResponse->getContent();
        $geonamesContentJSON = json_decode($geonamesContent,true);
        
        //$geonamesJSONfilesys = new Filesystem();
        //$geonamesJSONfilesys->dumpFile('geonames.json', $geonamesContent);

        foreach ($geonamesContentJSON["geonames"] as $geonamescolumn => $geonamesvalue) {
            echo "Value : <br/>";
            print_r($geonamesvalue);
            echo '<br/><br/>';
            //geonamesSubdivInsert($geonamesContentJSON["geonames"]);  
                if (!$geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId($geonamesvalue["geonameId"])) {
                    $geonamesSubDiv = new GeonamesAdministrativeDivision();
                    $geonamesSubDiv
                    ->setGeonameId($geonamesvalue["geonameId"])
                    ->setName($geonamesvalue["name"])
                    ->setToponymName($geonamesvalue["toponymName"])
                    ->setCountryCode($geonamesvalue["countryCode"])
                    ->setAdminName1($geonamesvalue["adminName1"])
                    ->setAdminCode1($geonamesvalue["adminCode1"])
                    //->setAdminCodes1($geonamesvalue["adminCodes1"])
                    ->setLat($geonamesvalue["lat"])
                    ->setLng($geonamesvalue["lng"])
                    ->setPopulation($geonamesvalue["population"])
                    ->setFcode($geonamesvalue["fcode"]);
                    $geonamesEntityManager->persist($geonamesSubDiv);

                    echo '<b>Insertion OK for '.$geonamesvalue["geonameId"].'</b><br/><br/>';
                }
                else {
                    echo '<b>'.$geonamesvalue["geonameId"]." Already found in database !</b><br/><br/>";
                }
            }
        $geonamesEntityManager->flush();

        return new Response(
            dd('<html><body><h1>Geonames</h1><h2>'
            . $this->sentence .
            '</h2>
            <section>'
            . $geonamesContent .
            '</section></body></html>')
        );
    }

    // ----------------------------------------------------------------
    //GLOBAL GET http://api.geonames.org/getJSON?geonameId=3035033&username=mathieugtr
    // ----------------------------------------------------------------
    #[Route('/geonames/globalgetjson/{geonamesId}', name: 'geonames_globalgetjson')]
    function geonamesGlobalGetJSON(EntityManagerInterface $geonamesEntityManager,int $geonamesId) {
        $geonamesUrl = 'http://api.geonames.org/getJSON?geonameId=' . $geonamesId . '&username=' . $this->token;

        if (!$geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)->findByGeonameId($geonamesId))
            {
                $geonamesClient = HttpClient::create();
                $geonamesResponse = $geonamesClient->request('GET', $geonamesUrl, ['timeout' => 30]);
                $geonamesContent = $geonamesResponse->getContent();
                $geonamesContentJSON = json_decode($geonamesContent,true);

                //dd($geonamesContentJSON);

                $geonamesSubDiv = new GeonamesAdministrativeDivision();

                // if(isset($geonamesContentJSON["alternateNames"][1]["name"]) && !empty($geonamesContentJSON["alternateNames"][1]["name"])){
                //     $geonamesSubDiv->setName($geonamesContentJSON["alternateNames"][1]["name"]);
                //     }
                
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

    #[Route('/geonames/{lat}-{lng}', name: 'geonames_latlng')]
    public function geonamesLatLng(EntityManagerInterface $geonamesEntityManager, int $lat, int $lng): Response
    {
        
        $geonamesIdResponse = $geonamesEntityManager->getRepository(GeonamesAdministrativeDivision::class)
                ->findByGeonameId();

        return new Response(
            dd($geonamesIdResponse)
        );
    }



}
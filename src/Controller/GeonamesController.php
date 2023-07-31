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
    #[Route('/geonames', name: 'geonames')]
    public function geonamesSearchJSON(EntityManagerInterface $entityManager): Response
    {
        $token = 'mathieugtr';
        $sentence = 'Hello, I\'m the controller.';
        //________________________Geonames global search (https://www.geonames.org/export/geonames-search.html)
        $geonamesUrl = 'http://api.geonames.org/searchJSON?maxRows=5&username=' . $token . '&featureCode=ADM1&style=FULL';

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
            
            $geonamesSubDiv = new GeonamesAdministrativeDivision();
        //    $GADmanager = $this->getEnti();
            //$geonamesSubDiv->save();
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
            // $entityManager->save();
            $entityManager->persist($geonamesSubDiv);

            echo "Insertion OK for ".$geonamesvalue["geonameId"]."<br/>";
            echo '<br/>';
            print_r($geonamesSubDiv);
            echo '<br/>_________________<br/>';
            print_r (gettype($geonamesSubDiv));
            echo '<br/>________________<br/>';
        }

        $entityManager->flush();

        return new Response(
            '<html><body><h1>Geonames</h1><h2>'
            . $sentence .
            '</h2>
            <section>'
            . $geonamesContent .
            '</section></body></html>'
        );
    }

    // public function geonamesSubdivInsert($geonamesvalue): Response
    // {
    //     // $geonamesSubDiv = new GeonamesAdministrativeDivision();
    //     //     //$geonamesSubDiv->save();
    //     //     $geonamesSubDiv->setGeonameId($geonamesvalue["geonameId"])->setName($geonamesvalue["name"])->setAsciiName($geonamesvalue["asciiName"])->setToponymName($geonamesvalue["toponymName"])->setContinentCode($geonamesvalue["continentCode"])->setCc2($geonamesvalue["cc2"] )->setCountryCode($geonamesvalue["countryCode"])->setAdminName1($geonamesvalue["adminName1"])->setAdminName2($geonamesvalue["adminName2"])->setAdminName3($geonamesvalue["adminName3"])->setAdminName4($geonamesvalue["adminName4"])->setAdminName5($geonamesvalue["adminName5"])->setAdminId1($geonamesvalue["adminId1"])->setAdminId2($geonamesvalue["adminId2"])->setAdminId3($geonamesvalue["adminId3"])->setAdminId4($geonamesvalue["adminId4"])->setAdminId5($geonamesvalue["adminId5"])->setAdminCode1($geonamesvalue["adminCode1"])->setAdminCode2($geonamesvalue["adminCode2"])->setAdminCode3($geonamesvalue["adminCode3"])->setAdminCode4($geonamesvalue["adminCode4"])->setLat($geonamesvalue["lat"])->setLng($geonamesvalue["lng"])->setPopulation($geonamesvalue["population"])->setTimezoneGmtOffset($geonamesvalue["timezoneGmtOffset"])->setTimezoneTimeZoneId($geonamesvalue["timezoneId"])->setTimezoneDstOffset($geonamesvalue["timezoneDstOffset"])->setAdminTypeName($geonamesvalue["adminTypeName"])->setFcode($geonamesvalue["fcode"]);
    //     //     $geonamesSubDiv->persist();
    //     //     $geonamesSubDiv->flush();
    //     //     echo "Insertion OK for ".$geonamesvalue["geonameId"]."<br/>";
    //     $value=0;

    //          return Response::$value;
    // }
}
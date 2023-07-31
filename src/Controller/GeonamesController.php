<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeonamesController
{
    #[Route('/geonames', name: 'geonames')]
    public function geonamesSearchJSON(): Response
    {

        $token = 'mathieugtr';
        $sentence = 'Hello, I\'m the controller.';
        $geonamesUrl = 'http://api.geonames.org/searchJSON?maxRows=5&username=' . $token . '&featureCode=ADM1';

        $geonamesClient = HttpClient::create();
        $geonamesResponse = $geonamesClient->request('GET', $geonamesUrl);
        $geonamesContent = $geonamesResponse->getContent();


        return new Response(
            '<html><body><h1>Geonames</h1><h2>'
            . $sentence .
            '</h2>
            <section><pre>'
            . $geonamesContent .
            '</pre></section></body></html>'
        );
    }
}
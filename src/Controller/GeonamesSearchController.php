<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeonamesSearchController extends AbstractController
{
    #[Route('/geonames/search', name: 'geonames_search', methods: ['POST'])]
    public function geonamesSearch(): Response
    {
        $request = Request::createFromGlobals();
        $requestContent = $request->getContent();
        dd(json_decode($requestContent));

        $response = new Response();

        return $response;
    }
}

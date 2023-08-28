<?php

namespace App\Controller;

use App\Service\GeonamesSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeonamesSearchController extends AbstractController
{
    #[Route('/geonames/search', name: 'geonames_search', methods: ['POST'])]
    public function geonamesSearch(GeonamesSearchService $searchService): Response
    {
        $request = json_decode(Request::createFromGlobals()->getContent(), true);
        $parsedRequest = $searchService->parseRequest($request);

        return new Response(json_encode($parsedRequest));
    }
}

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
        $response = new Response();
        $request = Request::createFromGlobals()->getContent();

        $parsedRequest = $searchService->bulkRequest($request);

        $response->setContent($parsedRequest);
        return $response;
    }
}

<?php

namespace App\Controller;

use App\Service\GeonamesSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeonamesSearchController extends AbstractController
{
    #[Route('/geonames/search', name: 'geonames_search', methods: ['POST'])]
    public function geonamesSearch(Request $request, GeonamesSearchService $searchService): Response
    {
        $response = new Response();
        $jsonPayloadString = $request->getContent();

        if (empty($jsonPayloadString)) {
            throw new BadRequestHttpException('Empty JISONE request');
        }

        $parsedRequest = $searchService->bulkRequest($jsonPayloadString);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($parsedRequest);

        return $response;
    }
}

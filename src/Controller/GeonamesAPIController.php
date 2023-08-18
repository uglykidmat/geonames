<?php

namespace App\Controller;

use App\Service\GeonamesAPIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GeonamesAPIController extends AbstractController
{
    #[Route('/geonamesapi/postalcodesearch/{postalcode}', name: 'api_postalcodesearch')]
    public function postalcodesearch(GeonamesAPIService $service, string $postalcode): JsonResponse
    {        
        $response = new JsonResponse($service->postalCodeSearchJSON($postalcode));

        return $response;
    }

    #[Route('/geonamesapi/postalcodelookup/{postalcode}-{countrycode}', name: 'api_postalcodelookup')]
    public function postalcodelookup(GeonamesAPIService $service, string $postalcode, string $countrycode): JsonResponse
    {        
        $response = new JsonResponse($service->postalCodeLookupJSON($postalcode, $countrycode));

        return $response;
    }
}

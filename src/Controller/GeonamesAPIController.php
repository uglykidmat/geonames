<?php

namespace App\Controller;

use App\Service\GeonamesAPIService;
use App\Service\GeonamesDBCachingService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/geonamesapi', name: 'api_home')]
class GeonamesAPIController extends AbstractController
{
    private Response $response;

    public function __construct()
    {
        $this->response = new Response();
        $this->response->headers->set('Content-Type', 'application/json');

    }

    #[Route('/postalcodesearch/{postalcode}', name: 'api_postalcodesearch')]
    public function postalCodeSearch(
        GeonamesAPIService $service,
        string $postalcode): JsonResponse
    {        
        $response = new JsonResponse($service->postalCodeSearchJSON($postalcode));

        return $response;
    }

    #[Route('/postalcodelookup/{postalcode}-{countrycode}', name: 'api_postalcodelookup')]
    public function postalCodeLookup(
        GeonamesAPIService $service,
        string $postalcode,
        string $countrycode): JsonResponse
    {        
        $response = new JsonResponse(
            $service->postalCodeLookupJSON(
                $postalcode,
                $countrycode));

        return $response;
    }

    #[Route('/latlng/{lat}-{lng}', name: 'api_latlng')]
    public function latLngSearch(
        GeonamesDBCachingService $dbcachingservice,
        GeonamesAPIService $apiservice,
        float $lat,
        float $lng): JsonResponse
    {
        $geonames = $apiservice->latLngSearch($lat, $lng);$dbcachingservice->saveSubdivisionToDatabase($geonames);
        
        return new JsonResponse($geonames);
    }
    
    #[Route('/subdivisions/{lat}-{lng}', name: 'api_subdivisions_by_latlng')]
    public function countrySubDivisionSearch(
        GeonamesAPIService $service,
        float $lat,
        float $lng): Response
    {
        return $service->countrySubDivisionSearch($lat, $lng);
    }
}

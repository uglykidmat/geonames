<?php

namespace App\Controller;

use App\Service\GeonamesAPIService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesDBCachingService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/geonamesapi', name: 'api_home')]
class GeonamesAPIController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private GeonamesAPIService $apiService,
    ) {
    }

    #[Route('/postalcodesearch/{postalcode}', name: 'api_postalcodesearch')]
    public function postalCodeSearch(
        string $postalcode
    ): JsonResponse {
        $response = new JsonResponse($this->apiService->postalCodeSearchJSON($postalcode));

        return $response;
    }

    #[Route('/postalcodelookup/{postalcode}-{countrycode}', name: 'api_postalcodelookup')]
    public function postalCodeLookup(
        string $postalcode,
        string $countrycode
    ): JsonResponse {
        $response = new JsonResponse(
            $this->apiService->postalCodeLookupJSON(
                $postalcode,
                $countrycode
            )
        );

        return $response;
    }

    #[Route('/latlng/{lat}-{lng}', name: 'api_latlng')]
    public function latLngSearch(
        GeonamesDBCachingService $dbcachingservice,
        float $lat,
        float $lng
    ): JsonResponse {
        $response = new JsonResponse();
        $geonameIdFound = $this->apiService->latLngSearch($lat, $lng);

        if (!$dbcachingservice->searchSubdivisionInDatabase($geonameIdFound)) {

            $dbcachingservice->saveSubdivisionToDatabase(
                $this->apiService->getJsonSearch($geonameIdFound)
            );
            $this->entityManager->flush();
        }

        $latlng = $this->serializer->serialize($dbcachingservice->searchSubdivisionInDatabase($geonameIdFound), 'json');

        return $response->setContent($latlng);
    }

    #[Route('/subdivisions/{lat}-{lng}', name: 'api_subdivisions_by_latlng')]
    public function countrySubDivisionSearch(
        float $lat,
        float $lng
    ): JsonResponse {
        $response = new JsonResponse();
        $response->setContent($this->apiService->countrySubDivisionSearch($lat, $lng)->getContent());

        return $response;
    }
}

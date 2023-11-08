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
use OpenApi\Attributes as OA;

#[Route('/geonamesapi', name: 'api_home')]
#[OA\Tag(name: 'Geonames API')]
class GeonamesAPIController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private GeonamesAPIService $apiService,
    ) {
    }

    #[Route('/postalcodelookup/{postalcode}/{countrycode}', name: 'api_postalcodelookup', methods: ['GET'])]
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

    #[Route('/latlng/{lat}-{lng}', name: 'api_latlng', methods: ['GET'])]
    public function latLngSearch(
        GeonamesDBCachingService $cachingService,
        float $lat,
        float $lng
    ): JsonResponse {
        $response = new JsonResponse();
        $geonameIdFound = $this->apiService->latLngSearch($lat, $lng);

        if (!$cachingService->searchSubdivisionInDatabase($geonameIdFound)) {

            $cachingService->saveSubdivisionToDatabase(
                $this->apiService->getJsonSearch($geonameIdFound)
            );
            $this->entityManager->flush();
        }

        $coordinates = $this->serializer->serialize($cachingService->searchSubdivisionInDatabase($geonameIdFound), 'json');

        return $response->setContent($coordinates);
    }
}

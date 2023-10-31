<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Service\GeonamesCountryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/country')]
#[OA\Tag(name: 'Geonames Countries general update, and barycenters computation')]
class GeonamesCountryController extends AbstractController
{
    public function __construct(
        private GeonamesCountryService $service
    ) {
    }
    #[Route('/update', methods: ['GET'])]
    public function updateCountries(): JsonResponse
    {
        $response = new JsonResponse();
        $this->service->purgeCountryList();
        $updateContent = explode(',', $this->service->getGeoCountryList()->getContent());
        $response->setContent(json_encode($updateContent));

        return $response;
    }

    #[Route('/barycenters/update', methods: ['GET'])]
    public function updateBarycenters(): Response
    {
        return $this->service->updateBarycenters();
    }

    #[Route('/barycenter/{countryCode}', methods: ['GET'])]
    public function computeBarycenter(string $countryCode): Response
    {
        return $this->service->computeBarycenter($countryCode);
    }
}

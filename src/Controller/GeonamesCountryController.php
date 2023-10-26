<?php

namespace App\Controller;

use App\Service\GeonamesCountryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[Route('/country')]
#[OA\Tag(name: 'Geonames Countries Barycenters')]
class GeonamesCountryController extends AbstractController
{
    public function __construct(
        private GeonamesCountryService $service
    ) {
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

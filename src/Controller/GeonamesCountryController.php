<?php

namespace App\Controller;

use App\Service\GeonamesCountryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/country')]
class GeonamesCountryController extends AbstractController
{
    public function __construct(
        private GeonamesCountryService $service
    ) {
    }

    #[Route('/barycenters')]
    public function updateBarycenters(): Response
    {
        return $this->service->updateBarycenters();
    }

    #[Route('/barycenter/{cc}')]
    public function computeBarycenter(string $cc): Response
    {
        return $this->service->computeBarycenter($cc);
    }
}

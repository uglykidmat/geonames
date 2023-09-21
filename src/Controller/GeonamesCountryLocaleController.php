<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\GeonamesCountryLocaleService;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;

class GeonamesCountryLocaleController extends AbstractController
{
    public function __construct(public GeonamesCountryLocaleService $localeService)
    {
    }


    #[Route('/countrynames/{locale}', name: 'countrynames', methods: ['GET', 'HEAD'])]
    public function getLangs(string $locale): JsonResponse
    {
        return $this->localeService->getCountryNamesForLocale($locale);
    }
}

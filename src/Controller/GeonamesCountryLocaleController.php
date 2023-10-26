<?php

namespace App\Controller;

use App\Service\GeonamesCountryLocaleService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

class GeonamesCountryLocaleController extends AbstractController
{
    public function __construct(
        private GeonamesCountryLocaleService $localeService,
    ) {
    }

    #[Route('/countrynames/{locale}', name: 'countrynames', methods: ['GET', 'HEAD'])]
    #[OA\Tag(name: 'Geonames Countries Locales')]
    public function getLangs(string $locale): JsonResponse
    {
        return $this->localeService->getCountryNamesForLocale($locale);
    }
}

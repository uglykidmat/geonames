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

    #[Route('/findCountryCodeByName/{name}', name: 'countrycodebyname', methods: ['GET', 'HEAD'])]
    #[OA\Tag(name: 'Geonames Country Code By Name')]
    public function findCountryCodeByName(string $name): JsonResponse
    {
        return $this->json($this->localeService->findCountryCodeByName($name));
    }
}

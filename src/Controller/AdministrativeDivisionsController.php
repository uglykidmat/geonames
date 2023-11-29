<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\AdministrativeDivisionsService;
use App\Service\AdministrativeDivisionLocaleService;
use App\Service\GeonamesCountryLocaleService;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/administrativedivisions')]
class AdministrativeDivisionsController extends AbstractController
{
    public function __construct(
        private AdministrativeDivisionsService $service,
        private AdministrativeDivisionLocaleService $localeService,
        private GeonamesCountryLocaleService $countrylocaleService
    ) {
    }

    #[Route('/api/{locale}/{countrycode}', name: 'subdivisions_api', methods: ['GET', 'HEAD'])]
    public function getSubdivisionsForApi(string $locale, string $countryCode): JsonResponse
    {
        return $this->service->getSubdivisionsForApi($locale, $countryCode);
    }

    #--5043------------------------------------------------------------
    #[Route('/export/{locale}/{level}', name: 'subdivisions_export', methods: ['GET', 'HEAD'])]
    public function getSubdivisions(string $locale, int $level): JsonResponse
    {
        return $this->json(
            $this->service->getSubdivisionsForExport($locale, $level)
        );
    }
    #------------------------------------------------------------------

    #[Route('/locales/update/{countrycode}', name: 'locales_update', methods: ['GET', 'HEAD'])]
    public function updateSubdivisionsLocales(string $countrycode): Response
    {
        return $this->localeService->updateSubdivisionsLocales($countrycode);
    }

    #[Route('/locales/{countrycode}/{locale}/{fcode}', name: 'locales_get', methods: ['GET', 'HEAD'])]
    public function showSubdivisionsLocales(string $countrycode, string $locale, string $fcode): Response
    {
        return $this->localeService->showSubdivisionsLocales($countrycode, strtolower($locale), $fcode);
    }

    #[Route('/alternatives/update', name: 'alternatives_update', methods: ['GET'])]
    public function updateAlternativeCodes(): Response
    {
        return $this->service->updateAlternativeCodes();
    }

    #[Route('/zipcodes', name: 'administrative_divisions_zipcodes', methods: ['GET'])]
    public function zipcodes(): Response
    {
        require_once __DIR__ . '/../../administrative_divisions/zipcodes/ZipCodes.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/zipcodesexceptions', name: 'administrative_divisions_zipcodesexceptions', methods: ['GET'])]
    public function zipcodesexceptions(): Response
    {
        require_once __DIR__ . '/../../administrative_divisions/zipcodes/ZipCodes_exceptions.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/txttojson', name: 'administrative_divisions_txttojson', methods: ['GET'])]
    public function txttojson(): Response
    {
        require_once __DIR__ . '/../../administrative_divisions/zipcodes/txt_to_json.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/jsonsortbyvalue', name: 'administrative_divisions_jsonsortbyvalue', methods: ['GET'])]
    public function jsonsortbyvalue(): Response
    {
        require_once __DIR__ . '/../../administrative_divisions/zipcodes/json_sort_by_value.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }
}

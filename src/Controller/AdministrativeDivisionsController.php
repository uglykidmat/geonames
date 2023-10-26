<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\AdministrativeDivisionsService;

#[Route('/administrativedivisions')]
class AdministrativeDivisionsController extends AbstractController
{
    public function __construct(private AdministrativeDivisionsService $service)
    {
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

    #[Route('/alternatives/update', name: 'alternatives_update', methods: ['GET'])]
    public function updateAlternativeCodes(): Response
    {
        return $this->service->updateAlternativeCodes();
    }
}

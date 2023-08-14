<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdministrativeDivisionsController extends AbstractController
{
    #[Route('/administrativedivisions/update', name: 'administrative_divisions_update')]
    public function index(): Response
    {
        require_once __DIR__.'/../../administrative_divisions/administrative_divisions_update.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/administrativedivisions/geojsonbackup', name: 'administrative_divisions_geojsonbackup')]
    public function geojsonbackup(): Response
    {
        require_once __DIR__.'/../../administrative_divisions/geojson_backup_script.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/administrativedivisions/zipcodes', name: 'administrative_divisions_zipcodes')]
    public function zipcodes(): Response
    {
        require_once __DIR__.'/../../administrative_divisions/zipcodes/ZipCodes.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }
    
    #[Route('/administrativedivisions/zipcodesexceptions', name: 'administrative_divisions_zipcodesexceptions')]
    public function zipcodesexceptions(): Response
    {
        require_once __DIR__.'/../../administrative_divisions/zipcodes/ZipCodes_exceptions.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/administrativedivisions/txttojson', name: 'administrative_divisions_txttojson')]
    public function txttojson(): Response
    {
        require_once __DIR__.'/../../administrative_divisions/zipcodes/txt_to_json.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

    #[Route('/administrativedivisions/jsonsortbyvalue', name: 'administrative_divisions_jsonsortbyvalue')]
    public function jsonsortbyvalue(): Response
    {
        require_once __DIR__.'/../../administrative_divisions/zipcodes/json_sort_by_value.php';

        return $this->render('administrative_divisions/index.html.twig', [
            'controller_name' => 'AdministrativeDivisionsController',
        ]);
    }

}

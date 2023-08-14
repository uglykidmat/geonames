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
}

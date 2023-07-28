<?php
// src/Controller/GeonamesController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeonamesController
{
    #[Route('/geonames', name: 'geonames')]
    public function hello(): Response
    {
        $sentence = "Hello, I'm the Geonames controller.";

        return new Response(
            '<html><body><h1>' . $sentence . '</h1></body></html>'
        );
    }
}
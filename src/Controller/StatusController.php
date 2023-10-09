<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatusController extends AbstractController
{
    #[Route('/status', name: 'status')]
    public function status(): JsonResponse
    {
        $response = new JsonResponse();
        $response->setContent(json_encode(['status' => 'up']));
        return $response;
    }
}

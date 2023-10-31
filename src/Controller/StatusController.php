<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

class StatusController extends AbstractController
{
    #[Route('/status', name: 'status', methods: ['GET'])]
    #[OA\Tag(name: 'Geonames Status')]
    public function status(): JsonResponse
    {
        $response = new JsonResponse();
        $response->setContent(json_encode(['status' => 'up']));
        return $response;
    }
}

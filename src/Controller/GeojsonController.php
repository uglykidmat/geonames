<?php

namespace App\Controller;

use App\Service\GeojsonService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[Route('/geojson', name: 'geojson')]
#[OA\Tag(name: 'Geonames GeoJson')]
class GeojsonController extends AbstractController
{
    public function __construct(
        public GeojsonService $service
    ) {
    }

    #[Route('/update', name: 'geojson_update', methods: ['GET', 'HEAD'])]
    public function update(): JsonResponse
    {
        return $this->service->updateAll();
    }

    #[Route('/get', name: 'geojson_get_all', methods: ['GET', 'HEAD'])]
    public function getAll(): JsonResponse
    {
        return $this->service->getAll();
    }

    #[Route('/get/{id}', name: 'geojson_get_one', methods: ['GET', 'HEAD'])]
    public function getOne($id): JsonResponse
    {
        return $this->service->getOne($id);
    }
}

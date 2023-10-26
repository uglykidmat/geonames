<?php

namespace App\Controller;

use App\Entity\GeonamesTranslation;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesTranslationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/translation')]
#[OA\Tag(name: 'Geonames Translations')]
class GeonamesTranslationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GeonamesTranslationService $translationService,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('/export', name: 'translation_export', methods: ['GET', 'HEAD'])]
    public function export(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . gmdate("Ymdgi") . '_geonames_translations.csv"');
        $translations = $this->serializer->serialize(
            $this->entityManager->getRepository(GeonamesTranslation::class)->findAll(),
            'csv'
        );

        return $response->setContent($translations);
    }

    #[Route('', name: 'translation_get', methods: ['GET', 'HEAD'])]
    public function get(): JsonResponse
    {
        $getResponse = $this->entityManager->getRepository(GeonamesTranslation::class)->findAll();
        $result = array_map(static fn (GeonamesTranslation $value): array => $value->toArray(), $getResponse);
        //TODO: return rendered paginated filtered list
        return new JsonResponse($result);
    }

    #[Route('', name: 'translation_post', methods: ['POST'])]
    public function post(
        Request $postRequest,
    ): JsonResponse {
        $postContent = json_decode($postRequest->getContent());
        $this->translationService->checkRequest($postRequest);
        $this->translationService->checkRequestContent($postContent);

        return $this->translationService->postTranslation($postContent);
    }

    #[Route('', name: 'translation_patch', methods: ['PATCH'])]
    public function patch(
        Request $patchRequest,
    ): JsonResponse {
        $patchContent = json_decode($patchRequest->getContent());
        $this->translationService->checkRequest($patchRequest);
        $this->translationService->checkRequestContent($patchContent);

        return $this->translationService->patchTranslation($patchContent);
    }

    #[Route('', name: 'translation_delete', methods: ['DELETE'])]
    public function delete(
        Request $deleteRequest,
    ): JsonResponse {
        $deleteContent = json_decode($deleteRequest->getContent());
        $this->translationService->checkRequest($deleteRequest);
        $this->translationService->checkRequestContent($deleteContent);

        return $this->translationService->deleteTranslation($deleteContent);
    }
}

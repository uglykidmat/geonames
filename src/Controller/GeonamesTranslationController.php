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

#[Route('/translation')]
class GeonamesTranslationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GeonamesTranslationService $translationService
    ) {
    }

    #[Route('/', name: 'translation_get', methods: ['GET', 'HEAD'])]
    public function get(): JsonResponse
    {
        $getResponse = $this->entityManager->getRepository(GeonamesTranslation::class)->findAll();

        $result = array_map(static fn (GeonamesTranslation $value): array => $value->toArray(), $getResponse);
        //TODO: return rendered paginated filtered list
        return new JsonResponse($result);
    }

    #[Route('/', name: 'translation_post', methods: ['POST'])]
    public function post(
        Request $postRequest,
    ): JsonResponse {
        $postContent = json_decode($postRequest->getContent());

        if ($this->translationService->checkRequest($postRequest)->getStatusCode() != 200) {
            return $this->translationService->checkRequest($postRequest);
        } else if ($this->translationService->checkRequestContent($postContent)->getStatusCode() != 200) {
            return $this->translationService->checkRequestContent($postContent);
        } else
            return $this->translationService->postTranslation($postContent, $this->entityManager);
    }

    #[Route('/', name: 'translation_patch', methods: ['PATCH'])]
    public function patch(
        Request $patchRequest,
    ): JsonResponse {
        $patchContent = json_decode($patchRequest->getContent());

        if ($this->translationService->checkRequest($patchRequest)->getStatusCode() != 200) {
            return $this->translationService->checkRequest($patchRequest);
        } else if ($this->translationService->checkRequestContent($patchContent)->getStatusCode() != 200) {
            return $this->translationService->checkRequestContent($patchContent);
        } else
            return $this->translationService->patchTranslation($patchContent, $this->entityManager);
    }

    #[Route('/', name: 'translation_delete', methods: ['DELETE'])]
    public function delete(
        Request $deleteRequest,
    ): JsonResponse {
        $deleteContent = json_decode($deleteRequest->getContent());

        if ($this->translationService->checkRequest($deleteRequest)->getStatusCode() != 200) {
            return $this->translationService->checkRequest($deleteRequest);
        } else if ($this->translationService->checkRequestContent($deleteContent)->getStatusCode() != 200) {
            return $this->translationService->checkRequestContent($deleteContent);
        } else
            return $this->translationService->deleteTranslation($deleteContent, $this->entityManager);
    }

    #[Route('/update', name: 'translation_update')]
    public function update(): Response
    {
        $translationResponse = '';
        $translationJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_translation.json'), true);

        foreach ($translationJson as $translationJsonKey => $translationJsonValue) {
            if (!$this->entityManager->getRepository(GeonamesTranslation::class)
                ->findByCountryCode($translationJsonValue["countryCode"])) {
                $translation = (new GeonamesTranslation())
                    ->setGeonameId($translationJsonValue["geonameId"])
                    ->setName($translationJsonValue["name"])
                    ->setCountryCode($translationJsonValue["countryCode"])
                    ->setFcode($translationJsonValue["fcode"])
                    ->setLocale($translationJsonValue["locale"]);

                $this->entityManager->persist($translation);
            } else {
                $translationResponse .= 'KO ';
            }
        }

        $this->entityManager->flush();

        return $this->render('translation/index.html.twig', [
            'controller_name' => 'GeonamesTranslationController',
            'response' => $translationResponse,
            'translation' => $translationJson
        ]);
    }
}

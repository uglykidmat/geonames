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
    #[Route('/', name: 'translation_get', methods: ['GET', 'HEAD'])]
    public function get(EntityManagerInterface $translationEntityManager): JsonResponse
    {
        $getResponse = $translationEntityManager->getRepository(GeonamesTranslation::class)->findAll();

        $result = array_map(static fn (GeonamesTranslation $value): array => $value->toArray(), $getResponse);
        //TODO: return rendered paginated filtered list
        return new JsonResponse($result);
    }

    #[Route('/', name: 'translation_post', methods: ['POST'])]
    public function post(Request $postRequest, EntityManagerInterface $translationEntityManager, GeonamesTranslationService $translationService): JsonResponse
    {
        $postContent = json_decode($postRequest->getContent());

        if ($translationService->checkRequest($postRequest)->getStatusCode() != 200) {
            return $translationService->checkRequest($postRequest);
        } else if ($translationService->checkRequestContent($postContent)->getStatusCode() != 200) {
            return $translationService->checkRequestContent($postContent);
        } else
            return $translationService->postTranslation($postContent, $translationEntityManager);
    }

    #[Route('/', name: 'translation_patch', methods: ['PATCH'])]
    public function patch(Request $patchRequest, EntityManagerInterface $translationEntityManager, GeonamesTranslationService $translationService): JsonResponse
    {
        $patchContent = json_decode($patchRequest->getContent());

        if ($translationService->checkRequest($patchRequest)->getStatusCode() != 200) {
            return $translationService->checkRequest($patchRequest);
        } else if ($translationService->checkRequestContent($patchContent)->getStatusCode() != 200) {
            return $translationService->checkRequestContent($patchContent);
        } else
            return $translationService->patchTranslation($patchContent, $translationEntityManager);
    }

    #[Route('/', name: 'translation_delete', methods: ['DELETE'])]
    public function delete(Request $deleteRequest, EntityManagerInterface $translationEntityManager, GeonamesTranslationService $translationService): JsonResponse
    {
        $deleteContent = json_decode($deleteRequest->getContent());

        if ($translationService->checkRequest($deleteRequest)->getStatusCode() != 200) {
            return $translationService->checkRequest($deleteRequest);
        } else if ($translationService->checkRequestContent($deleteContent)->getStatusCode() != 200) {
            return $translationService->checkRequestContent($deleteContent);
        } else
            return $translationService->deleteTranslation($deleteContent, $translationEntityManager);
    }

    #[Route('/update', name: 'translation_update')]
    public function update(EntityManagerInterface $translationEntityManager): Response
    {
        $translationResponse = '';
        $translationJson = json_decode(file_get_contents(__DIR__ . '/../../base_data/geonames_translation.json'), true);

        foreach ($translationJson as $translationJsonKey => $translationJsonValue) {
            if (!$translationEntityManager->getRepository(GeonamesTranslation::class)
                ->findByCountryCode($translationJsonValue["countryCode"])) {
                $translation = (new GeonamesTranslation())
                    ->setGeonameId($translationJsonValue["geonameId"])
                    ->setName($translationJsonValue["name"])
                    ->setCountryCode($translationJsonValue["countryCode"])
                    ->setFcode($translationJsonValue["fcode"])
                    ->setLocale($translationJsonValue["locale"]);

                $translationEntityManager->persist($translation);
            } else {
                $translationResponse .= 'KO ';
            }
        }

        $translationEntityManager->flush();

        return $this->render('translation/index.html.twig', [
            'controller_name' => 'GeonamesTranslationController',
            'response' => $translationResponse,
            'translation' => $translationJson
        ]);
    }
}

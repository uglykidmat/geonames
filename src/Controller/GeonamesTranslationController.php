<?php

namespace App\Controller;

use App\Entity\GeonamesTranslation;
use Doctrine\ORM\EntityManagerInterface;
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
        //TODO: return paginated filtered list
        return new JsonResponse($result);
    }

    #[Route('/', name: 'translation_post', methods: ['POST'])]
    public function post(Request $postRequest, EntityManagerInterface $translationEntityManager): JsonResponse
    {
        $postResponse = new JsonResponse();

        //________________________________Media type check
        if ($postRequest->getContentTypeFormat() != 'json' || !$postRequest->getContent()) {
            $postResponse->setStatusCode(415);
            $postResponse->setContent('{"Error" : "Unsupported Media Type, expected content-type application/JSON"}');

            return $postResponse;
        }

        //________________________________JSON Syntax check
        $postContent = (array) @json_decode($postRequest->getContent());

        if (!(json_last_error() === JSON_ERROR_NONE)) {
            $postResponse->setStatusCode(422);
            $postResponse->setContent('{"Json error" : "' . json_last_error_msg() . '"}');

            return $postResponse;
        }

        //________________________________Translation fields check
        $errorsInPostContent = false;

        foreach ($postContent as $postKey => $postValue) {
            $postValue = (array)$postValue;
            if (
                !empty($postValue["geonameId"]) ||
                !empty($postValue["name"]) ||
                !empty($postValue["countryCode"]) ||
                !empty($postValue["fcode"]) ||
                !empty($postValue["locale"]) ||
                $errorsInPostContent == true
            ) {
                $errorsInPostContent = true;
                $postResponse->setStatusCode(400);
                $postResponse->setContent('{"Json error" : "Missing fields or null values are not allowed"}');

                return $postResponse;
            }
        }

        $dbInsertionFound = array();
        $dbInsertionDone = array();

        //________________________________GeonameId check in repository

        foreach ($postContent as $postKey => $postValue) {

            $geonamesTranslation = $translationEntityManager
                ->getRepository(GeonamesTranslation::class)
                ->findByGeonameId($postValue["geonameId"]);

            $postValue = (array)$postValue;
            if ($geonamesTranslation) {
                $dbInsertionFound[] = $postValue["geonameId"];
            } else {
                $postTranslation = (new GeonamesTranslation())
                    ->setGeonameId($postValue["geonameId"])
                    ->setName($postValue["name"])
                    ->setCountryCode($postValue["countryCode"])
                    ->setFcode($postValue["fcode"])
                    ->setLocale($postValue["locale"]);

                $translationEntityManager->persist($postTranslation);

                $dbInsertionDone[] = $postValue["geonameId"];
            }
        }
        $translationEntityManager->flush();

        $postResponse->setStatusCode(201);

        if (count($dbInsertionDone) == 0) {
            $postResponse->setContent('{"POST" : "Success", "GeonameIds already found" : "' . implode(',', $dbInsertionFound) . '"}');
        } else if (count($dbInsertionFound) == 0) {
            $postResponse->setContent('{"POST" : "Success", "GeonameIds inserted" : "' . implode(',', $dbInsertionDone) . '"}');
        } else {
            $postResponse->setContent('{"POST" : "Success", "GeonameIds already found" : "' . implode(',', $dbInsertionFound) . '", "GeonameIds inserted" : "' . implode(',', $dbInsertionDone) . '"}');
        }

        return $postResponse;
    }

    #[Route('/', name: 'translation_patch', methods: ['PATCH'])]
    public function patch(Request $patchRequest, EntityManagerInterface $translationEntityManager): JsonResponse
    {
        $patchResponse = new JsonResponse();

        //________________________________Media type check
        if ($patchRequest->getContentTypeFormat() != 'json' || !$patchRequest->getContent()) {
            $patchResponse->setStatusCode(415);
            $patchResponse->setContent('{"Error" : "Unsupported Media Type, expected content-type application/JSON"}');

            return $patchResponse;
        }

        //________________________________JSON Syntax check
        $patchContent = (array) @json_decode($patchRequest->getContent());

        if (!(json_last_error() === JSON_ERROR_NONE)) {
            $patchResponse->setStatusCode(422);
            $patchResponse->setContent('{"Json error" : "' . json_last_error_msg() . '"}');

            return $patchResponse;
        }

        //________________________________GeonameId check in repository

        $dbPatchDone = array();

        foreach ($patchContent as $patchKey => $patchValue) {
            $translationToPatch = $translationEntityManager->getRepository(GeonamesTranslation::class)
                ->findByGeonameId($patchValue["geonameId"]);
            $patchValue = (array)$patchValue;
            if ($translationToPatch) {
                $translationToPatch = $translationToPatch[0];
                if (!empty($patchValue["geonameId"])) {
                    $translationToPatch->setGeonameId($patchValue["geonameId"]);
                }
                if (!empty($patchValue["name"])) {
                    $translationToPatch->setName($patchValue["name"]);
                }
                if (!empty($patchValue["countryCode"])) {
                    $translationToPatch->setCountryCode($patchValue["countryCode"]);
                }
                if (!empty($patchValue["fcode"])) {
                    $translationToPatch->setFcode($patchValue["fcode"]);
                }
                if (!empty($patchValue["locale"])) {
                    $translationToPatch->setLocale($patchValue["locale"]);
                }

                $translationEntityManager->persist($translationToPatch);
                $dbPatchDone[] = $patchValue["geonameId"];
            }
        }
        $translationEntityManager->flush();

        $patchResponse->setStatusCode(200);
        $patchResponse->setContent('{"PATCH" : "Success", "GeonameIds updated" : "' . implode(',', $dbPatchDone) . '"}');

        return $patchResponse;
    }

    #[Route('/', name: 'translation_delete', methods: ['DELETE'])]
    public function delete(Request $deleteRequest, EntityManagerInterface $translationEntityManager): JsonResponse
    {
        $deleteResponse = new JsonResponse();

        //________________________________Media type check
        if ($deleteRequest->getContentTypeFormat() != 'json' || !$deleteRequest->getContent()) {
            $deleteResponse->setStatusCode(415);
            $deleteResponse->setContent('{"Error" : "Unsupported Media Type, expected content-type application/JSON"}');

            return $deleteResponse;
        }

        //________________________________JSON Syntax check
        $deleteContent = (array) @json_decode($deleteRequest->getContent());

        if (!(json_last_error() === JSON_ERROR_NONE)) {
            $deleteResponse->setStatusCode(422);
            $deleteResponse->setContent('{"Json error" : "' . json_last_error_msg() . '"}');

            return $deleteResponse;
        }

        //________________________________GeonameId check in repository
        $dbDeleteDone = array();
        foreach ($deleteContent as $deleteKey => $deleteValue) {
            $deleteValue = (array)$deleteValue;
            if ($translationToDelete = $translationEntityManager->getRepository(GeonamesTranslation::class)
                ->findByGeonameId($deleteValue["geonameId"])
            ) {
                $translationToDelete = $translationToDelete[0];
                $translationEntityManager->remove($translationToDelete);

                $dbDeleteDone[] = $deleteValue["geonameId"];
            }
        }
        $translationEntityManager->flush();

        $deleteResponse->setStatusCode(200);
        $deleteResponse->setContent('{"DELETE" : "Success", "GeonameIds deleted" : "' . implode(',', $dbDeleteDone) . '"}');

        return $deleteResponse;
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

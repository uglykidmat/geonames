<?php

namespace App\Service;

use App\Entity\GeonamesCountryLocale;
use App\Entity\GeonamesTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GeonamesTranslationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function checkRequest(Request $postRequest): JsonResponse
    {
        $postResponse = new JsonResponse();
        $postResponse->setContent('{"Status": "Success"}');
        if ($postRequest->getContentTypeFormat() != 'json' || !$postRequest->getContent()) {
            $postResponse->setStatusCode(415);
            $postResponse->setContent('{"Status":"Failure","Error" : "Unsupported Media Type, expected content-type application/JSON"}');

            return $postResponse;
        }
        $postContent = (array) @json_decode($postRequest->getContent());
        if (!(json_last_error() === JSON_ERROR_NONE)) {
            $postResponse->setStatusCode(422);
            $postResponse->setContent('{"Status": "Failure","Error" : "' . json_last_error_msg() . '"}');

            return $postResponse;
        }
        return $postResponse;
    }

    public function checkRequestContent(array $postContent): JsonResponse
    {
        $postResponse = new JsonResponse();
        $postResponse->setContent('{"Status": "Success"}');
        $errorsInPostContent = false;
        foreach ($postContent as $postValue) {
            if (
                empty($postValue->geonameId) ||
                empty($postValue->name) ||
                empty($postValue->countryCode) ||
                empty($postValue->fcode) ||
                empty($postValue->locale) ||
                $errorsInPostContent == true
            ) {
                $errorsInPostContent = true;
                $postResponse->setStatusCode(400);
                $postResponse->setContent('{"Status": "Failure","Error" : "Missing fields or null values are not allowed"}');

                return $postResponse;
            }
        }
        return $postResponse;
    }

    public function getTranslations()
    {
    }

    public function postTranslation(array $postContent): JsonResponse
    {
        $postResponse = new JsonResponse();

        $dbInsertionFound = array();
        $dbInsertionDone = array();

        foreach ($postContent as $postValue) {

            if ($postValue->fcode == 'COUNTRY') {

                if ($countryLocale = $this->entityManager
                    ->getRepository(GeonamesCountryLocale::class)->findOneBy(array(
                        'geonameId' => $postValue->geonameId,
                        'locale' => $postValue->locale
                    ))
                ) {
                    $this->entityManager->remove($countryLocale);
                }

                $newCountryLocale = new GeonamesCountryLocale();
                $newCountryLocale
                    ->setGeonameId($postValue->geonameId)
                    ->setName($postValue->name)
                    ->setCountryCode($postValue->countryCode)
                    ->setLocale(strtolower($postValue->locale));
                $this->entityManager->persist($newCountryLocale);
            }

            if ($this->entityManager
                ->getRepository(GeonamesTranslation::class)
                ->findOneBy(array(
                    'geonameId' => $postValue->geonameId,
                    'locale' => $postValue->locale
                ))
            ) {
                $dbInsertionFound[] = $postValue->geonameId;
            } else {
                $postTranslation = (new GeonamesTranslation())
                    ->setGeonameId($postValue->geonameId)
                    ->setName($postValue->name)
                    ->setCountryCode($postValue->countryCode)
                    ->setFcode($postValue->fcode)
                    ->setLocale(strtolower($postValue->locale));
                $this->entityManager->persist($postTranslation);
                $dbInsertionDone[] = $postValue->geonameId;
            }
            $this->entityManager->flush();
        }

        if (count($dbInsertionDone) == 0) {
            $postResponse->setContent('{"POST" : "Success", "GeonameIds already found" : "' . implode(',', $dbInsertionFound) . '"}');
        } else if (count($dbInsertionFound) == 0) {
            $postResponse->setContent('{"POST" : "Success", "GeonameIds inserted" : "' . implode(',', $dbInsertionDone) . '"}');
            $postResponse->setStatusCode(201);
        } else {
            $postResponse->setContent('{"POST" : "Success", "GeonameIds already found" : "' . implode(',', $dbInsertionFound) . '", "GeonameIds inserted" : "' . implode(',', $dbInsertionDone) . '"}');
            $postResponse->setStatusCode(201);
        }

        return $postResponse;
    }

    public function patchTranslation(array $patchContent): JsonResponse
    {
        $patchResponse = new JsonResponse();
        $patchResponse->setContent("yob");
        $dbPatchDone = array();
        foreach ($patchContent as $patchValue) {
            if ($translationToPatch = $this->entityManager->getRepository(GeonamesTranslation::class)
                ->findOneBy(array(
                    'geonameId' => $patchValue->geonameId,
                    'locale' => $patchValue->locale
                ))
            ) {
                $translationToPatch
                    ->setName($patchValue->name)
                    ->setCountryCode($patchValue->countryCode)
                    ->setFcode($patchValue->fcode);
                $this->entityManager->persist($translationToPatch);
                $dbPatchDone[] = $patchValue->geonameId;
            }
        }
        $this->entityManager->flush();

        $patchResponse->setStatusCode(200);
        $patchResponse->setContent('{"PATCH" : "Success", "GeonameIds updated" : "' . implode(',', $dbPatchDone) . '"}');

        return $patchResponse;
    }

    public function deleteTranslation(array $deleteContent): JsonResponse
    {
        $deleteResponse = new JsonResponse();
        $dbDeleteDone = array();
        foreach ($deleteContent as $deleteValue) {

            if ($translationToDelete = $this->entityManager->getRepository(GeonamesTranslation::class)
                ->findOneBy(array(
                    'geonameId' => $deleteValue->geonameId,
                    'locale' => $deleteValue->locale
                ))
            ) {
                $this->entityManager->remove($translationToDelete);

                $dbDeleteDone[] = $deleteValue->geonameId;
            }
        }
        $this->entityManager->flush();

        $deleteResponse->setStatusCode(200);
        $deleteResponse->setContent('{"DELETE" : "Success", "GeonameIds deleted" : "' . implode(',', $dbDeleteDone) . '"}');

        return $deleteResponse;
    }
}

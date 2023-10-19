<?php

namespace App\Service;

use App\Entity\GeonamesCountryLocale;
use App\Entity\GeonamesTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class GeonamesTranslationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function checkRequest(Request $postRequest): void
    {
        if ($postRequest->getContentTypeFormat() !== 'json' || !$postRequest->getContent()) {
            throw new UnsupportedMediaTypeHttpException('Unsupported Media Type, expected content-type application/JSON');
        }
        if (!(json_last_error() === JSON_ERROR_NONE)) {
            throw new UnprocessableEntityHttpException(json_last_error_msg());
        }
    }

    public function checkRequestContent(array $postContent): void
    {
        foreach ($postContent as $postValue) {
            if (
                empty($postValue->geonameId) ||
                empty($postValue->name) ||
                empty($postValue->countryCode) ||
                empty($postValue->fcode) ||
                empty($postValue->locale)
            ) {
                throw new BadRequestHttpException('Missing fields or null values are not allowed for ' . print_r($postValue, true));
            }
        }
    }

    public function postTranslation(array $postContent): JsonResponse
    {
        $postResponse = new JsonResponse();
        $dbInsertionFound = array();
        $dbInsertionDone = array();

        foreach ($postContent as $postValue) {
            if ($postValue->fcode === 'COUNTRY') {
                if ($this->entityManager
                    ->getRepository(GeonamesCountryLocale::class)->findBy(array(
                        'geonameId' => $postValue->geonameId,
                        'locale' => $postValue->locale
                    ))
                ) {
                    $dbInsertionFound[] = 'Country ' . $postValue->geonameId;
                } else {
                    $newCountryLocale = new GeonamesCountryLocale();
                    $newCountryLocale
                        ->setGeonameId($postValue->geonameId)
                        ->setName($postValue->name)
                        ->setCountryCode($postValue->countryCode)
                        ->setLocale(strtolower($postValue->locale));
                    $this->entityManager->persist($newCountryLocale);
                }
            }

            if ($this->entityManager
                ->getRepository(GeonamesTranslation::class)
                ->findOneBy(array(
                    'geonameId' => $postValue->geonameId,
                    'locale' => $postValue->locale
                ))
            ) {
                $dbInsertionFound[] = 'SubDivision ' . $postValue->geonameId;
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
        $dbPatchDone = array();
        foreach ($patchContent as $patchValue) {
            if ($patchValue->fcode === 'COUNTRY') {
                if ($countryLocaleToPatch = $this->entityManager
                    ->getRepository(GeonamesCountryLocale::class)->findOneBy(array(
                        'geonameId' => $patchValue->geonameId,
                        'locale' => $patchValue->locale
                    ))
                ) {
                    $countryLocaleToPatch
                        ->setGeonameId($patchValue->geonameId)
                        ->setName($patchValue->name)
                        ->setCountryCode($patchValue->countryCode)
                        ->setLocale(strtolower($patchValue->locale));
                    $this->entityManager->persist($countryLocaleToPatch);
                    $dbPatchDone[] = $patchValue->geonameId;
                }
            }

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

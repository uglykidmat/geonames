<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AdministrativeDivisionLocale;
use App\Interface\GeonamesAPIServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdministrativeDivisionLocaleService
{
    public function __construct(
        public GeonamesAPIServiceInterface $apiservice,
        public EntityManagerInterface $entityManager,
        private CacheItemPoolInterface $redisCache,
        private string $redisDsn,
    ) {
    }

    public function getSubdivisionsLocales(string $fcode, string $locale): JsonResponse
    {
        $response = new JsonResponse();

        $result = $this->entityManager->getRepository(AdministrativeDivisionLocale::class)->findBy(
            [
                'fCode' => $fcode,
                'locale' => $locale
            ]
        );

        $response->setContent($result);

        return $response;
    }

    public function updateSubdivisionsLocales(string $fcode): JsonResponse
    {
        $response = new JsonResponse();

        //$countryResponse = $this->apiservice->searchJSON(string $fcode);

        //$response->setContent($result);

        return $response;
    }
}

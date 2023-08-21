<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesDBCachingService
{
    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

}

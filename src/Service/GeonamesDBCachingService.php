<?php
// src/Service/GeonameAdapterService.php
namespace App\Service;

use stdClass;
use App\Adapter\GeonamesAdapter;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesDBCachingService
{
    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function searchSubdivisionInDatabase(int $geonameId): array
    {
        $dbresponse = $this->entityManager
            ->getRepository(GeonamesAdministrativeDivision::class)
            ->findByGeonameId($geonameId);

        return $dbresponse;
    }

    public function saveSubdivisionToDatabase(stdClass $subdivision): void
    {
        $newSubDivision = GeonamesAdapter::AdaptObjToSubdiv($subdivision);
        $this->entityManager->persist($newSubDivision);
        $this->entityManager->flush();
    }
}

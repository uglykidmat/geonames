<?php
// src/Service/GeonameAdapterService.php
namespace App\Service;

use stdClass;
use App\Adapter\GeonamesAdapter;
use App\Service\GeonamesAPIService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesDBCachingService
{
    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $entityManager,
        private GeonamesAPIService $apiservice,
    ) {
    }

    public function searchSubdivisionInDatabase(int $geonameId): ?GeonamesAdministrativeDivision
    {
        $dbResponse = $this->entityManager
            ->getRepository(GeonamesAdministrativeDivision::class)
            ->findOneByGeonameId($geonameId);

        if ($dbResponse === null) {
            self::saveSubdivisionToDatabase(
                $this->apiservice->getJsonSearch($geonameId)
            );

            $dbResponse = $this->entityManager
                ->getRepository(GeonamesAdministrativeDivision::class)
                ->findOneByGeonameId($geonameId);
        }

        return $dbResponse;
    }

    public function saveSubdivisionToDatabase(stdClass $subdivision): void
    {
        $newSubDivision = GeonamesAdapter::AdaptObjToSubdiv($subdivision);
        $this->entityManager->persist($newSubDivision);
        //$this->entityManager->flush();
    }
}

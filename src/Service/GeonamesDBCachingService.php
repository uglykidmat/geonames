<?php
// src/Service/GeonameAdapterService.php
namespace App\Service;

use stdClass;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
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
            $this->entityManager->flush();

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
    }

    public function saveCountryToDatabase(stdClass $country): void
    {
        $newCountry = GeonamesAdapter::AdaptObjToCountry($country);
        $this->entityManager->persist($newCountry);
    }

    public function saveChildren(array $children): void
    {
        foreach ($children as $child) {
            $this->entityManager->persist($child);
        }
        $this->logger->info(
            '🍆 Flushing...'
        );
        $this->entityManager->flush();
    }
}

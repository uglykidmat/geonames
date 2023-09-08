<?php
// src/Service/GeonameAdapterService.php
namespace App\Service;

use Predis;
use stdClass;
use App\Adapter\GeonamesAdapter;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GeonamesAdministrativeDivision;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeonamesDBCachingService
{
    private RedisAdapter $cache;

    public function __construct(
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $entityManager,
        private string $redisDsn
    ) {
        $this->cache = new RedisAdapter(
            RedisAdapter::createConnection($redisDsn)
        );
    }

    public function searchSubdivisionInDatabase(int $geonameId): ?GeonamesAdministrativeDivision
    {
        $cacheKey = 'geonames_subdivision_' . $geonameId;

        $cachedData = $this->cache->getItem($cacheKey);

        if (!$cachedData->isHit()) {
            $dbResponse = $this->entityManager
                ->getRepository(GeonamesAdministrativeDivision::class)
                ->findOneByGeonameId($geonameId);

            $cachedData->set($dbResponse);
            $cachedData->expiresAfter(3600);
            $this->cache->save($cachedData);
        } else {
            $dbResponse = $cachedData->get();
        }

        return $dbResponse;
    }

    public function saveSubdivisionToDatabase(stdClass $subdivision): void
    {
        $newSubDivision = GeonamesAdapter::AdaptObjToSubdiv($subdivision);
        $this->entityManager->persist($newSubDivision);
        $this->entityManager->flush();
    }
}

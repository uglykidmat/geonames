<?php

namespace App\Repository;

use App\Entity\GeonamesTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GeonamesTranslation>
 *
 * @method GeonamesTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeonamesTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeonamesTranslation[]    findAll()
 * @method GeonamesTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeonamesTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeonamesTranslation::class);
    }

    public function findLocalesForGeoId($geonameId, $locale): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $query =
            'SELECT g.geoname_id AS "geonameId", g.country_code AS "countryCode", g.name AS "name" FROM geonames_translation as g WHERE g.locale = :locale AND g.geoname_id = :geonameid';
        $resultSet = $connection->executeQuery($query, ['locale' => $locale, 'geonameid' => $geonameId]);

        return $resultSet->fetchAllAssociative();
    }
}

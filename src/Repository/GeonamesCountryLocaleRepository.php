<?php

namespace App\Repository;

use App\Entity\GeonamesCountryLocale;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<GeonamesCountryLocale>
 *
 * @method GeonamesCountryLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeonamesCountryLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeonamesCountryLocale[]    findAll()
 * @method GeonamesCountryLocale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeonamesCountryLocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeonamesCountryLocale::class);
    }

    public function findLocales($locale): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $query =
            'SELECT g.geoname_id, g.country_code,
                (SELECT name FROM geonames_country_locale as gcl WHERE gcl.geoname_id = g.geoname_id AND gcl.locale = g.locale ORDER BY gcl.is_preferred_name, gcl.is_short_name LIMIT 1)  
            FROM geonames_country_locale as g WHERE g.locale = :locale GROUP BY g.geoname_id, g.locale, g.country_code';
        $resultSet = $connection->executeQuery($query, ['locale' => $locale]);

        return $resultSet->fetchAllAssociative();
    }
}

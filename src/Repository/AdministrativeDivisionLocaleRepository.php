<?php

namespace App\Repository;

use App\Entity\AdministrativeDivisionLocale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdministrativeDivisionLocale>
 *
 * @method AdministrativeDivisionLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdministrativeDivisionLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdministrativeDivisionLocale[]    findAll()
 * @method AdministrativeDivisionLocale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministrativeDivisionLocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdministrativeDivisionLocale::class);
    }

    //    /**
    //     * @return AdministrativeDivisionLocale[] Returns an array of AdministrativeDivisionLocale objects
    //     */
    // public function findByCountryCodeAndFCode($countrycode): array
    // {
    //     return $this->createQueryBuilder('a')
    //         ->andWhere('a.countryCode = :cc')
    //         ->setParameter('cc', $countrycode)
    //         ->andWhere('a.fCode = ADM1')
    //         ->orderBy('a.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult();
    // }

    public function findOneFallBack(int $geonameId, string $countryCode): ?AdministrativeDivisionLocale
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.geonameId = :id')
            ->setParameter('id', $geonameId)
            ->andWhere('a.locale = :locale')
            ->setParameter('locale', $countryCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLocalesForGeoId(int $geonameId, string $locale): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $query =
            'SELECT g.geoname_id AS "geonameId", g.country_code AS "countryCode",
                (SELECT name FROM administrative_division_locale as gcl WHERE gcl.geoname_id = g.geoname_id AND gcl.locale = g.locale ORDER BY gcl.is_preferred_name, gcl.is_short_name LIMIT 1)  
            FROM administrative_division_locale as g WHERE g.locale = :locale AND g.geoname_id = :geonameid GROUP BY g.geoname_id, g.locale, g.country_code';
        $resultSet = $connection->executeQuery($query, ['locale' => $locale, 'geonameid' => $geonameId]);

        return $resultSet->fetchAllAssociative();
    }
}

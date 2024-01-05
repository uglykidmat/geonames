<?php

namespace App\Repository;

use App\Entity\GeonamesCountryLocale;
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
        $queryBuilderGcl = $connection->createQueryBuilder('gcl');
        $queryBuilderGcli = $connection->createQueryBuilder('gcli');

        $queryBuilderGcli
            ->select('gcli.name')
            ->from('geonames_country_locale', 'gcli')
            ->andWhere('gcli.geoname_id = gcl.geoname_id')
            ->andWhere('gcli.locale = gcl.locale')
            ->orderBy('gcli.is_preferred_name, gcli.is_short_name')
            ->setFirstResult(0)
            ->setMaxResults(1);

        $queryBuilderGcl
            ->select('gcl.geoname_id as geonameId, gcl.country_code as countryCode', '(' . $queryBuilderGcli . ')')
            ->from('geonames_country_locale', 'gcl')
            ->andWhere('gcl.locale = :locale')
            ->setParameter('locale', $locale)
            ->getQueryParts();

        return $queryBuilderGcl->fetchAllAssociative();
    }

    public function findLocalesForGeoId($geonameId, $locale): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $queryBuilderGcl = $connection->createQueryBuilder('gcl');
        $queryBuilderGcli = $connection->createQueryBuilder('gcli');

        $queryBuilderGcli
            ->select('gcli.name')
            ->from('geonames_country_locale', 'gcli')
            ->andWhere('gcli.geoname_id = gcl.geoname_id')
            ->andWhere('gcli.locale = gcl.locale')
            ->orderBy('gcli.is_preferred_name, gcli.is_short_name')
            ->setFirstResult(0)
            ->setMaxResults(1);

        $queryBuilderGcl
            ->select('gcl.geoname_id as geonameId, gcl.country_code as countryCode', '(' . $queryBuilderGcli . ')')
            ->from('geonames_country_locale', 'gcl')
            ->andWhere('gcl.geoname_id = :geonameid')
            ->andWhere('gcl.locale = :locale')
            ->setParameter('geonameid', $geonameId)
            ->setParameter('locale', $locale)
            ->getQueryParts();

        return $queryBuilderGcl->fetchAllAssociative();
    }

    public function findCountryCodeByName(string $name): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $queryBuilder = $connection->createQueryBuilder('c');

        $queryBuilder->select('c.country_code')
            ->from('geonames_country_locale', 'c')
            ->andWhere('c.name iLIKE :name')
            ->setParameter('name', $name)
            ->groupBy('c.country_code');

        return $queryBuilder->fetchAllAssociative();
    }
}

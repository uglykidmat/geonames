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

    public function findOneFallBack(int $geonameId, string $countryCode): ?array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.geonameId = :id')
            ->setParameter('id', $geonameId)
            ->andWhere('a.locale = :locale')
            ->setParameter('locale', $countryCode)
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
    }

    public function findLocalesForGeoId(int $geonameId, string $locale): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $queryBuilderAdl = $connection->createQueryBuilder('adl');
        $queryBuilderAdli = $connection->createQueryBuilder('adli');

        $queryBuilderAdli
            ->select('adli.name')
            ->from('administrative_division_locale', 'adli')
            ->andWhere('adli.geoname_id = adl.geoname_id')
            ->andWhere('adli.locale = adl.locale')
            ->orderBy('adli.is_preferred_name, adli.is_short_name')
            ->setFirstResult(0)
            ->setMaxResults(1);

        $queryBuilderAdl
            ->select('adl.geoname_id as geonameId, adl.country_code as countryCode', '(' . $queryBuilderAdli . ')')
            ->from('administrative_division_locale', 'adl')
            ->andWhere('adl.geoname_id = :geonameid')
            ->andWhere('adl.locale = :locale')
            ->setParameter('geonameid', $geonameId)
            ->setParameter('locale', $locale)
            ->getQueryParts();

        return $queryBuilderAdl->fetchAllAssociative();
    }
}

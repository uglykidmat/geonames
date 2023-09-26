<?php

namespace App\Repository;

use App\Entity\GeonamesCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GeonamesCountry>
 *
 * @method GeonamesCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeonamesCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeonamesCountry[]    findAll()
 * @method GeonamesCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeonamesCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeonamesCountry::class);
    }

    //    /**
    //     * @return GeonamesCountry[] Returns an array of GeonamesCountry objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GeonamesCountry
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findGeoJson(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.geojson IS NOT null')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\GeonamesCountryLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GeonamesCountryLevel>
 *
 * @method GeonamesCountryLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeonamesCountryLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeonamesCountryLevel[]    findAll()
 * @method GeonamesCountryLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeonamesCountryLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeonamesCountryLevel::class);
    }

    //    /**
    //     * @return GeonamesCountryLevel[] Returns an array of GeonamesCountryLevel objects
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

    //    public function findOneBySomeField($value): ?GeonamesCountryLevel
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findUsedLevelMoreThan($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.usedLevel >= :val')
            ->andWhere('g.usedLevel != 0')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}

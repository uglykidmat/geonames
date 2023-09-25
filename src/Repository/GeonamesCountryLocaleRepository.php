<?php

namespace App\Repository;

use App\Entity\GeonamesCountryLocale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    //    /**
    //     * @return GeonamesCountryLocale[] Returns an array of GeonamesCountryLocale objects
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

    //    public function findOneBySomeField($value): ?GeonamesCountryLocale
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

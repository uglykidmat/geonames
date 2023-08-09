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

//    /**
//     * @return GeonamesTranslation[] Returns an array of GeonamesTranslation objects
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

//    public function findOneBySomeField($value): ?GeonamesTranslation
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

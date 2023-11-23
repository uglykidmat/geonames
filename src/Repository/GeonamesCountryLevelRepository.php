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

    public function findUsedLevelMoreThan($value): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.countryCode')
            ->andWhere('g.usedLevel >= :val')
            ->andWhere('g.usedLevel <> 0')
            ->setParameter('val', $value)
            ->orderBy('g.countryCode', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByUsedLevelLessThan(int $level)
    {
        return $this->createQueryBuilder('g')
            ->select('g.countryCode')
            ->andWhere('g.usedLevel <= :level')
            ->andWhere('g.usedLevel <> 0')
            ->setParameter('level', $level)
            ->orderBy('g.countryCode', 'ASC')
            ->getQuery()
            ->execute();
    }
}

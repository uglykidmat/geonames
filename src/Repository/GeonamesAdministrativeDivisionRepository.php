<?php

namespace App\Repository;

use App\Entity\GeonamesAdministrativeDivision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GeonamesAdministrativeDivision>
 *
 * @method GeonamesAdministrativeDivision|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeonamesAdministrativeDivision|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeonamesAdministrativeDivision[]    findAll()
 * @method GeonamesAdministrativeDivision[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeonamesAdministrativeDivisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeonamesAdministrativeDivision::class);
    }

//    /**
//     * @return GeonamesAdministrativeDivision[] Returns an array of GeonamesAdministrativeDivision objects
//     */
   public function findByGeonameId($value): array
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.geonameId = :val')
           ->setParameter('val', $value)
           ->orderBy('g.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneByGeonameId($value): ?GeonamesAdministrativeDivision
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.geonameId = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function findByLatLng($lat,$lng): ?GeonamesAdministrativeDivision
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.lat = :lat')
           ->setParameter('lat', $lat)
           ->andWhere('g.lng = :lng')
           ->setParameter('lng', $lng)
           ->getQuery()
           ->getOneOrNullResult()
        ;
   }


}

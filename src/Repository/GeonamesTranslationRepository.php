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

    public function findLocalesForGeoId($geonameId, $locale): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $queryBuilderT = $connection->createQueryBuilder('g');
        $queryBuilderT
            ->select(
                'g.geoname_id as geonameId,
            g.country_code as countryCode,
            g.name as name'
            )
            ->from('geonames_translation', 'g')
            ->andWhere('g.geoname_id = :geonameid')
            ->andWhere('g.locale = :locale')
            ->setParameter('geonameid', $geonameId)
            ->setParameter('locale', $locale);

        return $queryBuilderT->fetchAllAssociative();
    }
}

<?php

namespace App\Repository;

use App\Entity\EtatStage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EtatStage|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatStage|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatStage[]    findAll()
 * @method EtatStage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatStageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatStage::class);
    }

    // /**
    //  * @return EtatStage[] Returns an array of EtatStage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EtatStage
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

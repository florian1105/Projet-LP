<?php

namespace App\Repository;

use App\Entity\ResponsableDesStages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResponsableDesStages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponsableDesStages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsableDesStages[]    findAll()
 * @method ResponsableDesStages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsableDesStagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponsableDesStages::class);
    }

    // /**
    //  * @return ResponsableDesStages[] Returns an array of ResponsableDesStages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResponsableDesStages
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\InformationsClasses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationsClasses|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationsClasses|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationsClasses[]    findAll()
 * @method InformationsClasses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationsClassesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationsClasses::class);
    }

    // /**
    //  * @return InformationsClasses[] Returns an array of InformationsClasses objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InformationsClasses
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\InformationsGlobales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InformationsGlobales|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationsGlobales|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationsGlobales[]    findAll()
 * @method InformationsGlobales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationsGlobalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationsGlobales::class);
    }

    // /**
    //  * @return InformationsGlobales[] Returns an array of InformationsGlobales objects
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
    public function findOneBySomeField($value): ?InformationsGlobales
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

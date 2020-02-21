<?php

namespace App\Repository;

use App\Entity\StageForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StageForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method StageForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method StageForm[]    findAll()
 * @method StageForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageForm::class);
    }

    // /**
    //  * @return StageForm[] Returns an array of StageForm objects
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
    public function findOneBySomeField($value): ?StageForm
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

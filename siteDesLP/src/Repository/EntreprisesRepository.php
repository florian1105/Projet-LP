<?php

namespace App\Repository;

use App\Entity\Entreprises;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Entreprises|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprises|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprises[]    findAll()
 * @method Entreprises[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntreprisesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprises::class);
    }

    // /**
    //  * @return Entreprises[] Returns an array of Entreprises objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Entreprises
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllUnvalide()
    {
        return $this->createQueryBuilder('entreprise')
            ->andWhere('entreprise.valide = :val')
            ->setParameter('val', false)
            ->orderBy('entreprise.nom', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllValide()
    {
        return $this->createQueryBuilder('entreprise')
            ->andWhere('entreprise.valide = :val')
            ->setParameter('val', true)
            ->orderBy('entreprise.nom', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}

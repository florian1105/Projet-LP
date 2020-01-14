<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Entity\Classes;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    /**
     * Affiche l'arborescence des dossiers
     * visibles par une classe.
     * @param $classe La classe dont on veut
     *                les dossiers.
     * @return L'arborescence des dossiers
     *         de la classe.
     */
    public function getTreeClasse(Classes $classe)
    {
        /* Préparation du ResultSet des Cours */
        $rsm = (new ResultSetMapping())
        ->addEntityResult('App\Entity\Cours', 'c')
        // Les champs simples
        ->addFieldResult('c', 'id', 'id')
        ->addFieldResult('c', 'nom', 'nom')
        ->addFieldResult('c', 'visible', 'visible')
        // Les champs d'entités
        ->addMetaResult('c', 'cours_parent_id', 'cours_parent_id')
        ->addMetaResult('c', 'prof_id', 'prof_id');
        
        /* Requête récursive */
        return $this->getEntityManager()
        ->createNativeQuery("
            SELECT id, nom, cours_parent_id, prof_id, visible, @cr
            FROM (
                -- Tri les cours
                SELECT *
                FROM cours
                WHERE visible = TRUE
                ORDER BY cours_parent_id, id
            ) cours_tries,
            (
                -- Recupere la liste des cours racines accessibles par la classe
                SELECT @cr := 
                (
                    SELECT 
                    GROUP_CONCAT(cours.id) AS id
                    FROM cours
                    JOIN cours_classes ON cours_id = cours.id
                    WHERE classes_id = :cla_id
                    AND cours_parent_id IS NULL
                    GROUP BY classes_id
                )
            ) entree
            -- Affiche les dossiers racines
            WHERE find_in_set(id, @cr)
            -- Affiche les sous-dossiers
            OR find_in_set(cours_parent_id, @cr)
            -- Recursivitée
            AND length( @cr := concat(@cr, ',', id) )
        ", $rsm)
        ->setParameter('cla_id', $classe->getId())
        ->getResult();
    }

    // /**
    //  * @return Cours[] Returns an array of Cours objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cours
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

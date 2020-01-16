<?php

namespace App\Repository;

use App\Entity\Classes;
use App\Entity\Etudiants;
use App\Entity\Promotions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Etudiants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etudiants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etudiants[]    findAll()
 * @method Etudiants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiants::class);
    }

    public function getEtudiantsByPromotionAndClasse(Promotions $promotion, Classes $classe)
    {
      $qb = $this->createQueryBuilder("p")
          ->where(':classe MEMBER OF p.classes')
          ->setParameters(array('classe' => $classe));

        return $qb->getQuery()->getResult();
    }


    /**
     * Recupère les anciens étudiants.
     * @return Etudiants[] Returns an array of Etudiants objects
     */
    public function getAnciensEtudiants()
    {
        // Recupère la dernière année de promotion
        $res = $this->createQueryBuilder('e')
            ->select('p.anneeFin')
            ->from('App\Entity\Promotions', 'p')
            ->getQuery()
            ->getResult();

        // Si il n'y a pas de promotion on renvois l'annee en cours
        $anneeMax = getdate()['year'];
        if(!empty($res))
            $anneeMax = max($res)['anneeFin'];

        // Recupere les anciens etudiants
        return $this->createQueryBuilder('e')
            ->join('e.promotion', 'p')
            ->andWhere('p.anneeFin < :promo')
            ->setParameter('promo', $anneeMax)
            ->orderBy('e.login','ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function isAncienEtudiant(Etudiants $etu)
    {
        // Recupère la dernière année de promotion
        $res = $this->createQueryBuilder('e')
            ->select('p.anneeFin')
            ->from('App\Entity\Promotions', 'p')
            ->getQuery()
            ->getResult();

        // Si il n'y a pas de promotion on renvois l'annee en cours
        $anneeMax = getdate()['year'];
        if(!empty($res))
            $anneeMax = max($res)['anneeFin'];

        // Recupere les anciens etudiants
        $this->createQueryBuilder('e')
            ->join('e.promotion', 'p')
            ->andWhere('p.anneeFin < :promo')
            ->setParameter('promo', $anneeMax)
            ->where('p.id = :id')
            ->setParameter('id', $etu->getId())
            ->orderBy('e.login','ASC')
            ->getQuery()
            ->getResult()
        ;

        return true;
    }

}

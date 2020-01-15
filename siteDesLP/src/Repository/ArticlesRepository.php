<?php

namespace App\Repository;

use App\Entity\Classes;
use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    public function getArticleByClasse(Classes $classe)
    {
      $qb = $this->createQueryBuilder("p")
          ->where(':classe MEMBER OF p.classes')
          ->setParameters(array('classe' => $classe));
          
        return $qb->getQuery()->getResult();
    }

    public function getArticlePublicByClasse(Classes $classeUtilisateur)
    {
        $qb = $this->createQueryBuilder('p')
            ->addOrderBy('p.important', 'DESC')
            ->addOrderBy('p.date', 'DESC');

        $articles = $qb->getQuery()->getResult();

        for($i = 0; $i < sizeof($articles); $i++) 
        { 
            $garder = false;

            foreach ($articles[$i]->getClasses() as $classeArticle) //Pour chaque classe de l'article
            {
              if($classeArticle == $classeUtilisateur) $garder = true;
            }

            if($articles[$i]->getClasses()->count() == 0) $garder = true; //Si il est public on le garde

            if($garder != true) unset($articles[$i]);
        } 

        return $articles;
    }

    public function getArticlePublicTries()
    {
      $qb = $this->createQueryBuilder('p')
            ->addOrderBy('p.important', 'DESC')
            ->addOrderBy('p.date', 'DESC');

        $articles = $qb->getQuery()->getResult();

        for($i = 0; $i < sizeof($articles); $i++) 
        { 
            if($articles[$i]->getClasses()->count() != 0) unset($articles[$i]); //Si l'article a une classe on le supprime
        } 

        return $articles;
    }

    // /**
    //  * @return Articles[] Returns an array of Articles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

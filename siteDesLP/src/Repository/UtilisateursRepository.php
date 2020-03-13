<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use App\Repository\ContactRepository;
use App\Repository\EtudiantsRepository;
use App\Repository\SecretaireRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\ContactEntrepriseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Utilisateurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateurs[]    findAll()
 * @method Utilisateurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateurs::class);
    }

    public function mailExiste(string $mail, ManagerRegistry $registry)
    {
        $retour = false;

        $eRepo = new EtudiantsRepository($registry);
        $sRepo = new SecretaireRepository($registry);
        $pRepo = new ProfesseursRepository($registry);
        $cRepo = new ContactRepository($registry);
        $ceRepo = new ContactEntrepriseRepository($registry);

        if($this->findBy(['mail' => $mail]) != null) $retour = true;
        if($eRepo->findBy(['mailAcademique' => $mail]) != null) $retour = true;
        if($sRepo->findBy(['mailAcademique' => $mail]) != null) $retour = true;
        if($pRepo->findBy(['mailAcademique' => $mail]) != null) $retour = true;
        if($cRepo->findBy(['mail' => $mail]) != null) $retour = true;
        if($ceRepo->findBy(['mail' => $mail]) != null) $retour = true;

        return $retour;
    }

    // /**
    //  * @return Utilisateurs[] Returns an array of Utilisateurs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Utilisateurs
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

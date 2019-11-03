<?php

namespace App\DataFixtures;

use App\Entity\Etudiants;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class EtudiantFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=0; $i <10 ; $i++){
            $etudiant=new Etudiants();
            $int= rand(1262055681,1262055681);
             $date= new \DateTime("21-10-1990");
            $etudiant->setNomEtudiant("nom $i")
                ->setPrenomEtudiant("prenom $i")
                ->setLogin("nom$i"."Login")
                ->setDateNaissance($date)
                ->setMailAcademique("mailAcademique$i@yopmail.com")
                ->setMail("mail$i@yopmail.com")
                ->setPassword("passwd$i");
            $manager->persist($etudiant);

        }
        $manager->flush();
    }
}

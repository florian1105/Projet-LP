<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Etudiants;
use App\Entity\Classes;
use App\Repository\EtudiantsRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EtudiantController extends AbstractController
{
  
  public function str_to_noaccent($str)
  {
    $url = $str;
    $url = preg_replace('#Ç#', 'C', $url);
    $url = preg_replace('#ç#', 'c', $url);
    $url = preg_replace('#è|é|ê|ë#', 'e', $url);
    $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
    $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
    $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
    $url = preg_replace('#ì|í|î|ï#', 'i', $url);
    $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
    $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
    $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
    $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
    $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
    $url = preg_replace('#ý|ÿ#', 'y', $url);
    $url = preg_replace('#Ý#', 'Y', $url);
     
    return ($url);
  }
  

  /**
  * @Route("/etudiant/new", name="etudiant_create")
  * @Route("/etudiant/{id}/edit", name="etudiant_edit")
  */
  public function form(Etudiants $etudiant = null, Etudiantsrepository $repoE, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
  {
    $editMode = true;

    if(!$etudiant)
    {
      $etudiant = new Etudiants();
      $editMode = false;
    }

    if($editMode == false)
    {
      $form = $this->createFormBuilder($etudiant)
      ->add('nomEtudiant')
      ->add('prenomEtudiant')
      ->add('password', PasswordType::class)
      ->add('mail')
      ->add('dateNaissance', DateType::class, [
        'widget' => 'single_text'
      ])
      ->add('classeEtudiant', EntityType::class, [
        'class' => Classes::class,
        'choice_label' => 'nomClasse',
    ])

      ->getForm();

      $form->handleRequest($request);

      $prenomLogin = strtolower($this->str_to_noaccent($form['prenomEtudiant']->getData()));
      $prenomLogin1 = substr($prenomLogin, 0,1);
      $login = strtolower($form['nomEtudiant']->getData()).$prenomLogin1;
      $mailAcademique = $prenomLogin.".".strtolower($form['nomEtudiant']->getData());

      $i = "";
      $j = "";

      while($repoE->findBy(['login' => $login.$i]))
      {
        if($i == "") $i = 0;
        $i++;
      }

      while($repoE->findBy(['mailAcademique' => $mailAcademique.$j."@etu.umontpellier.fr"]))
      {
        if($j == "") $j = 0;
        $j++;
      }

      $etudiant->setLogin($login.$i);
      $etudiant->setMailAcademique($mailAcademique.$j."@etu.umontpellier.fr");

    }
    else
    {
      $form = $this->createFormBuilder($etudiant)
      ->add('nomEtudiant')
      ->add('prenomEtudiant')
      ->add('login')
      ->add('mail')
      ->add('mailAcademique')
      ->add('dateNaissance', DateType::class, [
        'widget' => 'single_text'
      ])
      ->add('classeEtudiant', EntityType::class, [
        'class' => Classes::class,
        'choice_label' => 'nomClasse',
      ])

      ->getForm();

      $form->handleRequest($request);

      $mailAca = strtolower($form['mailAcademique']->getData());
      $etudiant->setMailAcademique($mailAca);
    }

    $mail = strtolower($form['mail']->getData());
    $prenom = ucfirst(strtolower($form['prenomEtudiant']->getData()));
    $nom = strtoupper($form['nomEtudiant']->getData());

    $etudiant->setMail($mail);
    $etudiant->setNomEtudiant($nom);
    $etudiant->setPrenomEtudiant($prenom);

    if($editMode == false)
    {
      $hash = $encoder->encodePassword($etudiant, $etudiant->getPassword());
      $etudiant->setPassword($hash);
    }

    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($etudiant);
      $manager->flush();

      return $this->redirectToRoute('research_etudiant');

    }
    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
      'editMode' => $etudiant->getId() !== null,
      'etudiant' => $etudiant,
    ]);
  }

    /**
     * @Route("etudiant/etudiant_delete/{id}", name="etudiant_delete")
     */
  public function deleteEtudiant(Etudiants $etudiant)
  {
      $em = $this->getDoctrine()->getManager();
      $em->remove($etudiant);
      $em->flush();
      return $this->redirectToRoute("research_etudiant");
  }

    /**
     * @Route("etudiant/etudiant_research", name="research_etudiant")
     */
  public function researchEtudiant(EtudiantsRepository $repoE)
  {
      $etudiants =$repoE->findAll();
      return $this->render('etudiant/research.html.twig', [
          'etudiants' => $etudiants,
      ]);
  }

}

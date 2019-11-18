<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Entity\Etudiants;
use App\Repository\EtudiantsRepository;


use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
  public function form(Etudiants $etudiant = null, Etudiantsrepository $repoE, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
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
      $em->persist($etudiant);
      $em->flush();

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
  public function deleteEtudiant(Etudiants $etudiant, ObjectManager $em)
  {
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

      /**
     * @Route("etudiant_account", name="etudiant_account")
     */
    public function monCompte(UserInterface $etudiant)
    {
        $etudiant = $this->getUser();
        return $this->render('etudiant/moncompte.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

      /**
     * @Route("etudiant_account/change_password", name="change_password")
     */
    public function changePassword(UserInterface $etudiant, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {
        $etudiant = $this->getUser();

        $form = $this->createFormBuilder($etudiant)
      ->add('password', PasswordType::class)
      ->add('confirm_password', PasswordType::class)
    

      ->getForm();

      $form->handleRequest($request);

      $hash = $encoder->encodePassword($etudiant, $etudiant->getPassword());
      $etudiant->setPassword($hash);
    

    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($etudiant);
      $em->flush();

      return $this->redirectToRoute('etudiant_account');

    }

        return $this->render('etudiant/changepassword.html.twig', [
            'etudiant' => $etudiant,
            'form_change_password' => $form->createView()
        ]);
    }


     /**
     * @Route("etudiant_account/change_mail", name="change_mail")
     */
    public function changeMail(UserInterface $etudiant, Request $request, ObjectManager $em)
    {
        $etudiant = $this->getUser();

        $form = $this->createFormBuilder($etudiant)
      ->add('mail')
    

      ->getForm();

      $form->handleRequest($request);
    

    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($etudiant);
      $em->flush();

      return $this->redirectToRoute('etudiant_account');

    }

        return $this->render('etudiant/changepassword.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Etudiants;
use App\Entity\Classes;
use App\Repository\EtudiantsRepository;
use App\Repository\ClassesRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EtudiantController extends AbstractController
{
  /**
  * @Route("/etudiant/new", name="etudiant_create")
  * @Route("/etudiant/{id}/edit", name="etudiant_edit")
  */
  public function form(Etudiants $etudiant = null, Etudiantsrepository $repoE, Classesrepository $repoC, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
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
      ->add('mailAcademique')
      ->add('dateNaissance', DateType::class, [
        'widget' => 'single_text'])
      ->add('classeEtudiant', ChoiceType::class, [ 'choices' => [ 'nomClasse' => $repoC->findAll()]])

      ->getForm();

      $form->handleRequest($request);

      $prenom = strtolower($form['prenomEtudiant']->getData());
      $prenom1 = substr($prenom, 0,1);
      $login = strtolower($form['nomEtudiant']->getData()).$prenom1;
      
      $i = "";

      while($repoE->findBy(['login' => $login.$i]))
      {
        if($i == "") $i = 1;
        $i++;
      }

      $etudiant->setLogin($login.$i);

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

      ->getForm();
    }

    //$form->handleRequest($request);

    $classes = $repoC->findAll();

    if($editMode == false)
    {
      $hash = $encoder->encodePassword($etudiant, $etudiant->getPassword());
      $etudiant->setPassword($hash);
    }

    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($etudiant);
      $manager->flush();

      return $this->redirectToRoute('list');

    }
    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
      'editMode' => $etudiant->getId() !== null,
      'etudiant' => $etudiant,
      'classes' => $classes,
    ]);
  }

  /**
  * @Route("etudiant/etudiant_list", name="list")
  */
  public function showEtudiants(EtudiantsRepository $repoE)
  {
    $etudiants = $repoE->findAll();
    return $this->render('etudiant/list.html.twig', [
      'etudiants' => $etudiants,
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
      return $this->redirectToRoute('list');
  }

    /**
     * @Route("etudiant/etudiant/{id}", name="etudiant_info")
     */
  public function showEtudiantInfo(Etudiants $etudiant)
  {
      return $this->render('etudiant/info.html.twig', [
          'etudiant' => $etudiant,
      ]);
  }
    /**
     * @Route("etudiant/etudiant_research", name="research")
     */
  public function researchEtudiant(EtudiantsRepository $repoE)
  {
      $etudiants =$repoE->findAll();
      return $this->render('etudiant/research.html.twig', [
          'etudiants' => $etudiants,
      ]);
  }

}

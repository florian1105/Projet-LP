<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Etudiants;
use App\Repository\EtudiantsRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EtudiantController extends AbstractController
{
  /**
  * @Route("/etudiant/new", name="etudiant_create")
  * @Route("/etudiant/{id}/edit", name="etudiant_edit")
  */
  public function form(Etudiants $etudiant = null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
  {
    if(!$etudiant) $etudiant = new Etudiants();

    $form = $this->createFormBuilder($etudiant)
    ->add('nomEtudiant')
    ->add('prenomEtudiant')
    ->add('login')
    ->add('password', PasswordType::class)
    ->add('mail')
    ->add('mailAcademique')
    ->add('dateNaissance', DateType::class, [
      'widget' => 'single_text'
    ])

    ->getForm();

    $form->handleRequest($request);

    // $prenom = strtolower($form['prenomEtudiant']->getData());
    // $prenom1 = substr($prenom, 0,1);
    // $login = strtolower($form['nomEtudiant']->getData()).$prenom1;
    // $etudiant->setLogin($login);

    if(!$etudiant)
    {
      $hash = $encoder->encodePassword($etudiant, $etudiant->getPassword());
      $etudiant->setPassword($hash);
    }

    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($etudiant);
      $manager->flush();

      if($etudiant) return $this->redirectToRoute('list');

    }
    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
      'editMode' => $etudiant->getId() !== null,
      'etudiant' => $etudiant,
    ]);
  }

  /**
  * @Route("etudiant/etudiant_list", name="list")
  */
  public function showEtudiants(EtudiantsRepository $repo)
  {
    $etudiants = $repo->findAll();
    return $this->render('etudiant/list.html.twig', [
      'etudiants' => $etudiants,
    ]);
  }

}

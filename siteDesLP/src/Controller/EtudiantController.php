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
  * @Route("/etudiant_create", name="create")
  */
  public function create(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
  {
    $etudiant = new Etudiants();

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

    $hash = $encoder->encodePassword($etudiant, $etudiant->getPassword());
    $etudiant->setPassword($hash);

    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($etudiant);
      $manager->flush();


      $etudiant_new = new Etudiants();
      $form = $this->createFormBuilder($etudiant_new)
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
  }
    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
    ]);
  }

  /**
  * @Route("/etudiant_edit", name="edit")
  */
  public function showEtudiants(EtudiantsRepository $repo)
  {
    $etudiants = $repo->findAll();
    return $this->render('etudiant/edit.html.twig', [
      'etudiants' => $etudiants,
    ]);
  }

  /**
  * @Route("/etudiant_edit/{id}", name="edit_show")
  */
  public function edit(EtudiantsRepository $repo, Request $request, $id)
  {
    $etudiant = $repo->find($id);

    $form = $this->createFormBuilder($etudiant)
    ->add('nomEtudiant', TextType::class, ['label' => 'Nom étudiant'])
    ->add('prenomEtudiant', TextType::class, ['label' => 'Prénom étudiant'])
    ->add('mail', TextType::class, ['label' => 'E-mail'])
    ->add('mailAcademique', TextType::class, ['label' => 'E-mail académique'])
    ->add('login')
    ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
    ->add('dateNaissance', DateType::class, [
      'widget' => 'single_text',
      'label' => 'Date de naissance'
    ])

    ->getForm();

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
      $prenom = strtolower($form['prenomEtudiant']->getData());
      $prenom1 = substr($prenom, 0,1);
      $login = strtolower($form['nomEtudiant']->getData()).$prenom1;
      $etudiant->setLogin($login);
      $manager->persist($etudiant);
      $manager->flush();

      
      $form = $this->createFormBuilder($etudiant)
      ->add('nomEtudiant')
      ->add('prenomEtudiant')
      ->add('password', PasswordType::class)
      ->add('mail')
      ->add('mailAcademique')
      ->add('dateNaissance', DateType::class, [
        'widget' => 'single_text'
      ])

      ->getForm();


    }


    return $this->render('etudiant/show.html.twig', [
      'etudiant' => $etudiant,
      'form_create_etudiant' => $form->createView(),
    ]);
  }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Etudiants;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class EtudiantController extends AbstractController
{
  /**
  * @Route("/etudiant_create", name="create")
  */
  public function create(Request $request, ObjectManager $manager)
  {
    $etudiant = new Etudiants();

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

    $form->handleRequest($request);



    if($form->isSubmitted() && $form->isValid())
    {
      $prenom = strtolower($form['prenomEtudiant']->getData());
      $prenom1 = substr($prenom, 0,1);
      $login = strtolower($form['nomEtudiant']->getData()).$prenom1;
      $etudiant->setLogin($login);
      $manager->persist($etudiant);
      $manager->flush();

      
      $etudiant_new = new Etudiants();
      $form = $this->createFormBuilder($etudiant_new)
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

    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
    ]);
  }
}

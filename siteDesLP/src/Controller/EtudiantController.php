<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Etudiants;

class EtudiantController extends AbstractController
{
  /**
  * @Route("/etudiant_create", name="create")
  */
  public function create()
  {
    $etudiant = new Etudiants();

    $form = $this->createFormBuilder($etudiant)
    ->add('nomEtudiant',TextType::class,[
      'attr' => [
        'placeholder'=>"Nom de l'étudiant",
        'class' => 'form-control'
      ]
    ])
    ->add('prenomEtudiant',TextType::class,[
      'attr' => [
        'placeholder'=>"Prénom de l'étudiant",
        'class' => 'form-control'
      ]
    ])
    ->add('login',TextType::class,[
      'attr' => [
        'placeholder'=>"Login de l'étudiant",
        'class' => 'form-control'
      ]
    ])
    ->add('password',TextType::class,[
      'attr' => [
        'placeholder'=>"Password de l'étudiant",
        'class' => 'form-control'
      ]
    ])

    ->add('mail',TextType::class,[
      'attr' => [
        'placeholder'=>"Mail personnel de l'étudiant",
        'class' => 'form-control'
      ]
    ])
    ->add('mailAcademique',TextType::class,[
      'attr' => [
        'placeholder'=>"Mail académique",
        'class' => 'form-control'
      ]
    ])
    ->add('save', SubmitType::class,[
      'label' => 'Créer l\'étudiant',
      'attr' => ['class' =>'btn btn-success']
    ])
    ->getForm();




    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
    ]);
  }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    ->add('login')
    ->add('password')
    ->add('mail')
    ->add('mailAcademique')

    ->getForm();

    $form->handleRequest($request);



    if($form->isSubmitted() && $form->isValid())
    {
      $etudiant -> setDateNaissance(new \DateTime());
      $manager->persist($etudiant);
      $manager->flush();
    }

    return $this->render('etudiant/index.html.twig', [
      'form_create_etudiant' => $form->createView(),
    ]);
  }
}

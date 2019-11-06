<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Classes;
use App\Repository\ClassesRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class ClasseController extends AbstractController
{
    /**
     * @Route("/classe/new", name="classe_create")
     * @Route("/classe/{id}/edit", name="classe_edit")
     */
  public function form(Classes $classe = null, ClassesRepository $repo, Request $request, ObjectManager $manager)
  {
    $editMode = true;

    if(!$classe) 
    {
      $classe = new Classes();
      $editMode = false;
    }

    if($editMode == false)
    {
      $form = $this->createFormBuilder($classe)
      ->add('nomClasse')

      ->getForm();

      $form->handleRequest($request);

    }
    else
    {
      $form = $this->createFormBuilder($classe)
      ->add('nomClasse')

      ->getForm();

      $form->handleRequest($request);
    }

    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($classe);
      $manager->flush();

      return new Response('Classe créer');

    }

    return $this->render('classe/index.html.twig', [
      'form_create_classe' => $form->createView(),
      'editMode' => $classe->getId() !== null,
      'classe' => $classe,
    ]);
  }

  /**
  * @Route("classe/classe_list", name="classe_list")
  */
  public function showClasses(ClassesRepository $repoC)
  {
    $classes = $repoC->findAll();
    return $this->render('classe/list.html.twig', [
      'classes' => $classes,
    ]);
  }
}

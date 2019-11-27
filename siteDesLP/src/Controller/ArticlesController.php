<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Entity\Classes;
use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Doctrine\Common\Persistence\ObjectManager;




class ArticlesController extends AbstractController
{
  /**
  * @Route("/article_add", name="article_add")
  * @Route("/article/edit/{id}", name="article_edit")
  */
  public function form(Articles $article = null,Request $request, ObjectManager $em)
  {
    if(!$article)
    {
    $article = new Articles();
    $form = $this->createFormBuilder($article)
    ->add('titre')
    ->add('description', CKEditorType::class, [
      'config' => [
        'uiColor' => '#e2e2e2',
        'toolbar' => 'full',
        'required' => 'true'
      ]
    ])
    ->add('classes', EntityType::class, [
      'class' => Classes::class,
      'choice_label' => 'nomClasse',
      'label' => "Classe(s) concernées par l'article",
      'expanded' => true,
      'multiple' => true,
      'mapped' => false, //décoché par défaut
      'by_reference' => false,
    ])
    ->getForm();

    $form->handleRequest($request);
    $article->setDate(new \DateTime());

    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($article);
      $em->flush();
      return $this->redirectToRoute('article_search');
    }



  }
  //Edit mode ON
  else {
    $form = $this->createFormBuilder($article)
    ->add('titre')
    ->add('description', CKEditorType::class, [
      'config' => [
        'uiColor' => '#e2e2e2',
        'toolbar' => 'full',
        'required' => 'true'
      ]
    ])
    ->add('classes', EntityType::class, [
      'class' => Classes::class,
      'choice_label' => 'nomClasse',
      'label' => "Classe(s) concernées par l'article",
      'expanded' => true,
      'multiple' => true,
      'mapped' => true, //décoché par défaut
    ])
    ->getForm();

    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($article);
      $em->flush();
      return $this->redirectToRoute('articles');
    }

  }



    return $this->render('articles/index.html.twig', [
      'form_article' => $form->createView(),
      'editMode' => $article->getId() !== null,
    ]);
  }

  /**
  * @Route("/articles", name="article_search")
  */
  public function research(ArticlesRepository $repoA)
  {
    $articles = $repoA->findAll();
    return $this->render('articles/research.html.twig', [
      'articles' => $articles
    ]);

  }

  /**
  * @Route("/article/remove/{id}", name="article_delete")
  */
  public function delete(Articles $article, Request $request, ObjectManager $em)
  {

    //Si le formulaire à été soumis
    if($request->isMethod('POST'))
    {
      // En cas de validation on supprime et on redirige
      if($request->request->has('oui'))
      {
          $em->remove($article);
          $em->flush();
          $this->addFlash('delete',"Cet article a été supprimé avec succès");
          return $this->redirectToRoute('article_search');
      }

    }
    else
    {
      //Si le formulaire n'a pas été soumis alors on l'affiche
      $title = 'Êtes-vous sûr(e) de vouloir supprimer cet article ?';

      $message = 'Article n°'.$article->getId().' ayant pour titre : '.
      $article->getTitre().' datant du '.
      $article->getDate()->format('Y-m-d');

      return $this->render('confirmation.html.twig', [
        'titre' => $title,
        'message' => $message
      ]);
    }
  }
}
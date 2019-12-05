<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class ArticlesController extends AbstractController
{
  /**
  * @Route("/article/add", name="article_add")
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
      ->add('photo' , FileType::class)
      ->add('classes', EntityType::class,
      [
        'class' => Classes::class,
        'choice_label' => 'nomClasse',
        'multiple' => 'true',
        'expanded' => 'true',
        'required' => 'true',
        'mapped' => 'true'
      ])
      ->getForm();

      $form->handleRequest($request);
      $article->setDate(new \DateTime);
    }
    else
    { // Mode edit

      $form = $this->createFormBuilder($article)
      ->add('titre')
      ->add('description', CKEditorType::class, [
        'config' => [
          'uiColor' => '#e2e2e2',
          'toolbar' => 'full',
          'required' => 'true'
        ]
      ])
      ->add('photo' , FileType::class, array('data_class' => null,'required' => false))
      ->add('classes', EntityType::class,
      [
        'class' => Classes::class,
        'choice_label' => 'nomClasse',
        'label' => 'Classes de l\'article',
        'expanded' => true,
        'multiple' => true,
        'mapped' => true, //décoché par défaut
        'by_reference' => false,
      ])
      ->getForm();

      $form->handleRequest($request);
    }

    // Réception du form valide -> add/update
    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($article);
      $em->flush();
      $this->addFlash('success','les changements on biens été pris en compte');
      return $this->redirectToRoute('article_search');
    }


    return $this->render('articles/index.html.twig', [
      'form_article' => $form->createView(),
      'editMode' => $article->getId() !== null,
      'articles' => $article/*,
      'classes' => $classes*/
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

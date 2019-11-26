<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Entity\Classes;
use App\Entity\Articles;
use Doctrine\Common\Persistence\ObjectManager;




class ArticlesController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function create(Request $request, ObjectManager $em)
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
        }
        dump($article);
        $this->redirectToRoute('connexion');

        return $this->render('articles/index.html.twig', [
            'form_article' => $form->createView()
        ]);
    }
}

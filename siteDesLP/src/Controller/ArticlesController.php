<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\CKEditorBundle\Form\Type\CKEditorType;




class ArticlesController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function create()
    {
      $form = $this->createFormBuilder()
        ->add('content', CKEditorType::class, [
          'config' => [
            'uiColor' => '#e2e2e2',
            'toolabar' => 'full',
            'required' => 'true'
          ]
        ])
        ->getForm();

        return $this->render('articles/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

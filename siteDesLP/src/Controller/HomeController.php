<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticlesRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticlesRepository $repoA)
    {

        $articles = $repoA->findAll();
        return $this->render('home/index.html.twig', [
          'articles' => $articles
        ]);
    }
}

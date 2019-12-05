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
      $articles = array();
      $lesArticles = $repoA->findAll(); //tous les articles

      for($i = 0; $i < sizeof($lesArticles); $i++) 
      { 
        if($lesArticles[$i]->getClasses()->count() == 0) $articles[] = $lesArticles[$i]; //Si l'article n'a pas de classe(s) on le récupère
      }

      if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_ETUDIANT") //Si l'utilisateur est connecté et est un étudiant
      {
        $currentClasse = $this->getUser()->getClasseEtudiant();
        foreach ($repoA->getArticleByClasse($currentClasse) as $article) 
        {
          $articles[] = $article;
        }

        dump($articles);
      }

      //krsort($articles, "date");

        return $this->render('home/index.html.twig', [
          'articles' => $articles
        ]);
    }
}

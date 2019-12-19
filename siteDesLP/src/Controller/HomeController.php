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
      $articlesImportant = array(); //les articles importants
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
        
      }
      krsort($articles);

      $nbArticle = sizeof($articles);
      for($i = 0;$i < $nbArticle;$i++)
      {
        if($articles[$i]->getImportant() == true) 
        {
          
          $articlesImportant[$i] = $articles[$i];
          unset($articles[$i]);
        }
      }
      krsort($articlesImportant);
      $articles = array_merge($articlesImportant, $articles);


      return $this->render('home/index.html.twig', [
          'articles' => $articles
        ]);
    }
}

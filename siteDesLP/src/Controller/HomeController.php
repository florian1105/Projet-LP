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
      if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_ETUDIANT") //Si l'utilisateur est connecté et est un étudiant
      {
        $classe = $this->getUser()->getClasse();
        $articles = $repoA->getArticlePublicByClasse($classe); //On récupère les articles de sa classe en plus des articles publics
      }
      else $articles = $repoA->getArticlePublicTries();

      return $this->render('home/index.html.twig', [
          'articles' => $articles
        ]);
    }
}

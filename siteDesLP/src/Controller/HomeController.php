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
        $currentClasse = $this->getUser()->getClasseEtudiant();
        $articles = $repoA->getArticleByClasse($currentClasse);
      }
      else
      {
        $articles = $repoA->findAll();
      }

        return $this->render('home/index.html.twig', [
          'articles' => $articles
        ]);
    }
}

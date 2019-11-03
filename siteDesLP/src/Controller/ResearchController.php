<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResearchController extends AbstractController
{
    /**
     * @Route("/etudiant_research", name="research")
     */
    public function index()
    {
        return $this->render('etudiant/research.html.twig', [
            //'controller_name' => 'HomeController',
        ]);
    }
}

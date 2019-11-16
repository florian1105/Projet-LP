<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion()
    {
        return $this->render('connexion/index.html.twig');
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     */
    public function deconnexion() {}
}

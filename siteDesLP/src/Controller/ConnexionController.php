<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ConnexionController extends AbstractController
{
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(AuthenticationUtils $authUtils)
    {
      if($this->getUser())
      {
        throw new AccessDeniedException("Vous êtes déjà connecté, impossible d'accéder au formulaire de connexion, veuillez vous déconnecter afin de changer de compte.");
      }

      $error = $authUtils->getLastAuthenticationError();
        return $this->render('connexion/index.html.twig' , [
          'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     */
    public function deconnexion() {}
}

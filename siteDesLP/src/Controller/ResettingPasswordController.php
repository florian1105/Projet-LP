<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Repository\EtudiantsRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\SecretaireRepository;
use App\Services\Mailer;
use App\Form\ResettingPasswordType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class ResettingPasswordController extends AbstractController
{
    /**
     * @Route("/resetting/password", name="resetting_password")
     */
    public function index()
    {
        return $this->render('resetting_password/index.html.twig', [
            'controller_name' => 'ResettingPasswordController',
        ]);
    }

    /**
   * @Route("resetpasswordrequest", name="reset_password_request")
   */
    public function resetPasswordRequest($user = null, Request $request, Mailer $mailer, TokengeneratorInterface $tokenGenerator, ObjectManager $em, EtudiantsRepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS)
    {
      $form = $this->createFormBuilder()
      ->add('email', EmailType::class)
      ->getForm();

      $form->handleRequest($request);


      if($form->isSubmitted() && $form->isValid())
      {
        //On teste si il existe un utilisateur avec ce mail
        $user = $repoE->findOneBy(['mailAcademique' => $form['email']->getData()]);
        if(!$user)
        {

          $user = $repoP->findOneBy(['mailAcademique' => $form['email']->getData()]);
        }
        elseif(!$user)
        {
          $user = $repoS->findOneBy(['mailAcademique'=> $form['email']->getData()]);
        }
        //si il n'existe pas d'utilisateur avec ce mail on redirige sur la même page avec un message erreur
        if(!$user)
        {
          $this->addFlash('badMail',"Cet email n'existe pas, veuillez réessayer");
          return $this->redirectToRoute('reset_password_request');
        }
        //On affecte un token et une date de demande de mot de passe(correspond à maintenant)
        $user->setToken($tokenGenerator->generateToken());
        $user->setPasswordRequestedAt(new \Datetime());
        $em->flush();

        //Crée le corps du mail en utilisant le template mail_reset_password
        $bodyMail = $mailer->createBodyMail('resetting_password/mail_reset_password.html.twig', [
          'user' => $user
        ]);
        //envoie du mai
        $mailer->sendMessage('sitedeslp@gmail.com', $user->getMailAcademique(), 'Renouvellement de votre mot de passe sur le site des LP', $bodyMail);
        $this->addFlash('goodMail',"Un mail va vous être envoyé afin que vous puissez renouveller votre mot de passe, le lien que vous recevrez sera valide 24h.");
        return $this->redirectToRoute('connexion');

      }

      return $this->render('resetting_password/reset_password_request.html.twig',[
        'form' => $form->createView()
      ]);

    }

    //si la rêquete de mot de passe a été envoyé il y a plus de 24h retourne false
    //sinon retourne true
    private function isRequestedInTime(\DateTimeInterface $passwordRequestedAt = null)
    {
      if($passwordRequestedAt === null)
      {
        return false;
      }

      $now = new \Datetime();
      $interval = $now->getTimeStamp() - $passwordRequestedAt->getTimeStamp();

      $daySeconds = 86400;
      if($interval > $daySeconds)
      {
        $response =  false;
      }
      $response = true;

      return $response;
    }


    /**
   * @Route("resetpassword/{id}/{token}", name="resetting_password")
   */
    public function resetPassword($user = null, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder,ContactRepository $repoC,ObjectManager $em,  EtudiantsRepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS)
    {
      // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token de l'étudiant et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes

        //On vérifie qu'il existe un utilisateur avec le token en paramètre
        $user = $repoE->findOneBy(['token' => $token]);

        if (!$user)
        {
          $user = $repoP->findOneBy(['token' => $token]);
        }
        elseif(!$user)
        {
            $user = $repoC->findOneBy(['token' => $token]);

        }elseif(!$user)
        {
            $user = $repoS->findOneBy(['token' => $token]);
        }


        if(!$user){
            throw new AccessDeniedHttpException("Utilisateur non trouvé");
        }

        if(!$user || !$this->isRequestedInTime($user->getPasswordRequestedAt()))
        {
          throw new AccessDeniedHttpException("Votre demande de mot de passe a expiré, veuillez en faire une autre s'il vous plait.");
        }


        $form = $this->createForm(ResettingPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          //On affecte à l'utilisateur le nouveau mot de passe(celui saisit dans le formulaire)
          $hash = $passwordEncoder->encodePassword($user, $form['password']->getData());
          $user->setPassword($hash);

          //réinitialisation du token et de la date de demande de mdp à null pour empêcher de les réutiliser
          $user->setToken(null);
          $user->setPasswordRequestedAt(null);

          $em->persist($user);
          $em->flush();

          $this->addFlash('mdpReset',"Votre mot de passe a été modifié");

          return $this->redirectToRoute('connexion');

        }

        return $this->render('resetting_password/reset_password.html.twig',[
          'form' => $form->createView()
        ]);


    }

}

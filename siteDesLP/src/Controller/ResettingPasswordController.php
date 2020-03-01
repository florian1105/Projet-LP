<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Repository\EtudiantsRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\ResponsableDesStagesRepository;
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
   * @Route("requete_reinitialiser_mot_de_passe", name="requete_reinitialiser_mot_de_passe")
   */
    public function demandeReinitialisationMotDePasse($user = null, Request $request, Mailer $mailer, TokengeneratorInterface $tokenGenerator, EntityManagerInterface $em, EtudiantsRepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS, ResponsableDesStagesRepository $repoR)
    {
      $form = $this->createFormBuilder()
      ->add('email', EmailType::class)
      ->getForm();

      $form->handleRequest($request);


      if($form->isSubmitted() && $form->isValid())
      {
        $etudiant = $repoE->findOneBy(['mailAcademique' => $form['email']->getData()]);
        $secretaire = $repoS->findOneBy(['mailAcademique' => $form['email']->getData()]);
        $professeur = $repoP->findOneBy(['mailAcademique' => $form['email']->getData()]);
        $responsableDesStages = $repoR->findOneBy(['mailAcademique' => $form['email']->getData()]);

        $lesUtilisateurs = array($etudiant,$secretaire,$professeur, $responsableDesStages);

        $user = $this->getUserNonNull($lesUtilisateurs);

        //si il n'existe pas d'utilisateur avec ce mail on redirige sur la même page avec un message erreur
        if($user == null)
        {
          $this->addFlash('badMail',"Cet email n'existe pas, veuillez réessayer");
          return $this->redirectToRoute('requete_reinitialiser_mot_de_passe');
        }

        else
        {
          //On affecte un token et une date de demande de mot de passe(correspond à maintenant)
          $user->setToken($tokenGenerator->generateToken());
          $user->setPasswordRequestedAt(new \Datetime());
          $em->flush();

          //Crée le corps du mail en utilisant le template mail_reset_password
          $bodyMail = $mailer->createBodyMail('resetting_password/mail_reset_password.html.twig', [
            'user' => $user
          ]);
          //envoie du mail
          $mailer->sendMessage('sitedeslp@gmail.com', $user->getMailAcademique(), 'Renouvellement de votre mot de passe sur le site des LP', $bodyMail);
          $this->addFlash('goodMail',"Un mail va vous être envoyé afin que vous puissez renouveller votre mot de passe, le lien que vous recevrez sera valide 24h.");
          return $this->redirectToRoute('connexion');
        }


      }

      return $this->render('resetting_password/reset_password_request.html.twig',[
        'form' => $form->createView()
      ]);

    }

    //si la rêquete de mot de passe a été envoyé il y a plus de 24h retourne false
    //sinon retourne true
    private function demandeReinitialisationMotDePasseEstValide(\DateTimeInterface $passwordRequestedAt = null)
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
   * @Route("reinitialiser_mot_de_passe/{id}/{token}", name="reinitialiser_mot_de_passe")
   */
    public function reinitialiserMotDePasse($user = null, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder,ContactRepository $repoC,EntityManagerInterface $em,  EtudiantsRepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS, ResponsableDesStagesRepository $repoR)
    {
      // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token de l'étudiant et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes

        //On vérifie qu'il existe un utilisateur avec le token en paramètre
        $user = $repoE->findOneBy(['token' => $token]);

        if ($user==null)
        {
          $user = $repoP->findOneBy(['token' => $token]);
        }
        if($user==null)
        {
            $user = $repoC->findOneBy(['token' => $token]);
        }
        if($user==null)
        {
            $user = $repoS->findOneBy(['token' => $token]);
        }

        if($user==null)
        {
            $user = $repoR->findOneBy(['token' => $token]);
        }
        if(!$user){
            throw new AccessDeniedHttpException("Utilisateur non trouvé");
        }

        if(!$user || !$this->demandeReinitialisationMotDePasseEstValide($user->getPasswordRequestedAt()))
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

    public function getUserNonNull(array $tab)
    {
      $userNonNull = "";
      foreach ($tab as $unUser )
      {
        if($unUser !=null )
        {
          $userNonNull = $unUser;
        }
      }
      return $userNonNull;
    }

}

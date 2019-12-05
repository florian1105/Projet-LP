<?php

namespace App\Controller;

use App\Entity\Secretaire;
use App\Repository\EtudiantsRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\SecretaireRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecretaireController extends AbstractController
{
    /**
     * @Route("/secretaire/new", name="secretaire_add")
     * @Route("/secretaire/edit/{id}", name="secretaire_edit")
     */
    public function form(Secretaire $secretaire = null, ProfesseursRepository $repoP, SecretaireRepository $repoS, EtudiantsRepository $repoE, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        if(!$secretaire)
        {
            $secretaire = new Secretaire();

            $form = $this->createFormBuilder($secretaire)
                ->add('nomSecretaire')
                ->add('prenomSecretaire')
                ->add('new_password', PasswordType::class,
                    [
                        'attr' => ['maxlength' => '64']
                    ])
                ->add('confirm_password', PasswordType::class,
                    [
                        'attr' => ['maxlength' => '64'],
                    ])
                ->getForm();

            $form->handleRequest($request);


            // Gestion des champs générés
            $prenom = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(trim($secretaire->getPrenomSecretaire())));

            $nom = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(trim($secretaire->getNomSecretaire())));

            $mailAcademique = $prenom.".".$nom;

            $prenom = substr($prenom, 0,1);
            $login = strtolower($nom).$prenom;

            $i = "";
            $j = "";

            while($repoE->findBy(['login' => $login.$i]) || $repoP->findBy(['login' => $login.$i]) || $repoS->findBy(['login' => $login.$i]))
            {
                if($i == "") $i = 0;
                $i++;
            }

            while($repoS->findBy(['mailAcademique' => $mailAcademique.$j."@umontpellier.fr"]) || $repoP->findBy(['mailAcademique' => $mailAcademique.$j."@umontpellier.fr"]))
            {
                if($j == "") $j = 0;
                $j++;
            }

            $secretaire->setLogin($login.$i);
            $secretaire->setMailAcademique($mailAcademique.$j."@umontpellier.fr");

            $prenom = ucfirst(strtolower($form['prenomSecretaire']->getData()));
            $nom = strtoupper($form['nomSecretaire']->getData());

            $secretaire->setNomSecretaire($nom);
            $secretaire->setPrenomSecretaire($prenom);

            // Encodage du mot de passe

            $editmode = 0;
        }
        else
        { // Mode edit
            $editmode = 1;
            $form = $this->createFormBuilder($secretaire)
                ->add('nomSecretaire')
                ->add('prenomSecretaire')
                ->add('login')
                ->add('mailAcademique')
                //->add('password', PasswordType::class)

                ->getForm();

            $form->handleRequest($request);
        }

        // Réception du form valide -> add/update
        if($form->isSubmitted() && $form->isValid())
        {
            if(!$editmode){
                $hash = $encoder->encodePassword($secretaire, $secretaire->getNewPassword());
                $secretaire->setPassword($hash);
            }

            $manager->persist($secretaire);
            $manager->flush();
            if(!$editmode){$this->addFlash('success','la/le secrétaire a bien été créée');}
            else{$this->addFlash('success_modifie','la/le secrétaire a bien été modifié');}
            return $this->redirectToRoute('secretaire_search');
        }


        return $this->render('secretaire/index.html.twig', [
            'form' => $form->createView(),
            'editMode' => $secretaire->getId() !== null,
            'secretaire' => $secretaire
        ]);
    }


    /**
     * @Route("/secretaire/remove/{id}", name="secretaire_delete")
     */
    public function delete(Secretaire $secretaire, Request $req)
    {
        //Si le formulaire à été soumis
        if($req->isMethod('POST'))
        {
            // En cas de validation on supprime et on redirige
            if($req->request->has('oui'))
            {

                $em=$this->getDoctrine()->getManager();
                $em->remove($secretaire);
                $em->flush();
                $this->addFlash('delete',"la/le secretaire a été supprimé avec succès");
            }
            else{$this->addFlash('delete',"Aucun(e) secretaire n'a été supprimé");}
            return $this->redirectToRoute('secretaire_search');
        }
        else
        {
            //Si le formulaire n'a pas été soumis alors on l'affiche
            $title = 'Êtes-vous sûr(e) de vouloir supprimer cette secretaire ?';

            $message = 'N°'.$secretaire->getId().' : '.
                $secretaire->getPrenomSecretaire().' '.
                $secretaire->getNomSecretaire(). ' ('.
                $secretaire->getLogin().')';

            return $this->render('confirmation.html.twig', [
                'titre' => $title,
                'message' => $message
            ]);
        }
    }

    /**
     * @Route("/secretaire/search", name="secretaire_search")
     */
    public function search(SecretaireRepository $repo)
    {
        return $this->render('secretaire/research.html.twig', [
            'secretaires' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("secretaire_account", name="secretaire_account")
     */
    public function monCompte(UserInterface $secretaire)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException("L'administrateur n'a pas accès à ceci.");
        }

        $secretaire = $this->getUser();

        return $this->render('secretaire/moncompte.html.twig', [
            'secretaire' => $secretaire,
        ]);
    }

    /**
     * @Route("secretaire_account/change_password", name="secretaire_change_password")
     */
    public function changePassword(UserInterface $secretaire, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {

        $secretaire = $this->getUser();

        $form = $this->createFormBuilder($secretaire)
            ->add('password', PasswordType::class, array('mapped' => false))
            ->add('new_password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)


            ->getForm();


        $form->handleRequest($request);

        $mdpChange = "";
        $mdpNonChange = "";

        if($form->isSubmitted() && $form->isValid())
        {
            $match = $encoder->isPasswordValid($secretaire, $form['password']->getData());
            //si password valide
            if($match)
            {
                $hash = $encoder->encodePassword($secretaire, $form['new_password']->getData());
                $secretaire->setPassword($hash);
                $em->persist($secretaire);
                $em->flush();
                $this->addFlash('success', 'Mot de passe modifié ! ');
                return $this->redirectToRoute('secretaire_account');
            }

            else {
                $mdpNonChange = "Le mot de passe entré n'est pas votre mot de passe actuel";
            }

        }



        return $this->render('secretaire/changepassword.html.twig', [
            'secretaire' => $secretaire,
            'form_change_password' => $form->createView(),
            'error' => $mdpNonChange,
        ]);
    }

}

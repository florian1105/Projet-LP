<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Entity\Candidats;
use App\Repository\CandidatsRepository;
use App\Repository\EtudiantsRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\SecretaireRepository;
use Doctrine\Common\Persistence\ObjectManager;
use League\Csv\Reader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CandidatsController extends AbstractController
{
    public function str_to_noaccent($str)
    {
        $url = $str;
        $url = preg_replace('#Ç#', 'C', $url);
        $url = preg_replace('#ç#', 'c', $url);
        $url = preg_replace('#è|é|ê|ë#', 'e', $url);
        $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
        $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
        $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
        $url = preg_replace('#ì|í|î|ï#', 'i', $url);
        $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
        $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
        $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
        $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
        $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
        $url = preg_replace('#ý|ÿ#', 'y', $url);
        $url = preg_replace('#Ý#', 'Y', $url);

        return ($url);
    }


    /**
     * @Route("/candidats/new", name="candidat_create")
     * @Route("/candidats/{id}/edit", name="candidat_edit")
     */
    public function form(Candidats $Candidat = null, Candidatsrepository $repoC, Etudiantsrepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {

        $editMode = true;

        if(!$Candidat)
        {
            $Candidat = new Candidats();
            $editMode = false;
        }
        if($this->getUser()->getRoles()[0] == "ROLE_PROFESSEURRESPONSABLE") //Si l'utilisateur est un professeur responsable
        {
            if($editMode == false)
            {
                $form = $this->createFormBuilder($Candidat)
                    ->add('nom')
                    ->add('prenom')
                    ->add('new_password', PasswordType::class, [
                        'attr' => ['maxlength' => '64']
                    ])
                    ->add('confirm_password', PasswordType::class, [
                        'attr' => ['maxlength' => '64'],
                    ])
                    ->add('mail')
                    ->add('date_Naissance', DateType::class, [
                        'widget' => 'single_text'
                    ])

                    ->getForm();

                $form->handleRequest($request);

            }
            else
            {

                $form = $this->createFormBuilder($Candidat)
                    ->add('nom')
                    ->add('prenom')
                    ->add('mail')
                    ->add('date_Naissance', DateType::class, [
                        'widget' => 'single_text'
                    ])

                    ->getForm();

                $form->handleRequest($request);
            }

            $mail = strtolower($form['mail']->getData());
            $prenom = ucfirst(strtolower($form['prenom']->getData()));
            $nom = strtoupper($form['nom']->getData());

            $Candidat->setMail($mail);
            $Candidat->setNom($nom);
            $Candidat->setPrenom($prenom);


            if($form->isSubmitted() && $form->isValid())
            {
                if($editMode == false)
                {
                    $hash = $encoder->encodePassword($Candidat, $Candidat->getNewPassword());
                    $Candidat->setPassword($hash);
                }
                $em->persist($Candidat);
                $em->flush();


                return $this->redirectToRoute('research_candidat');

            }
        }
        else if($editMode == false) //Sinon si l'utilisateur n'est pas prof responsable
        {
            $form = $this->createFormBuilder($Candidat)
                ->add('nom')
                ->add('prenom')
                ->add('new_password', PasswordType::class, [
                    'attr' => ['maxlength' => '64']
                ])
                ->add('confirm_password', PasswordType::class, [
                    'attr' => ['maxlength' => '64'],
                ])
                ->add('mail')
                ->add('date_Naissance', DateType::class, [
                    'widget' => 'single_text'
                ])

                ->getForm();

            $form->handleRequest($request);

        }
        else
        {
            $form = $this->createFormBuilder($Candidat)
                ->add('nom')
                ->add('prenom')
                ->add('mail')
                ->add('date_Naissance', DateType::class, [
                    'widget' => 'single_text'
                ])

                ->getForm();

            $form->handleRequest($request);

        }

        $mail = strtolower($form['mail']->getData());
        $prenom = ucfirst(strtolower($form['prenom']->getData()));
        $nom = strtoupper($form['nom']->getData());

        $Candidat->setMail($mail);
        $Candidat->setNom($nom);
        $Candidat->setPrenom($prenom);



        if($form->isSubmitted() && $form->isValid())
        {
            if($editMode == false)
            {
                $hash = $encoder->encodePassword($Candidat, $Candidat->getNewPassword());
                $Candidat->setPassword($hash);
                $this->addFlash('success','le candidat a bien été créé');
            }
            else{
                $this->addFlash('success_modifie','les changements on biens été pris en compte');
            }
            $em->persist($Candidat);
            $em->flush();


            return $this->redirectToRoute('research_candidat');

        }

        return $this->render('candidats/index.html.twig', [
            'form_create_candidat' => $form->createView(),
            'editMode' => $Candidat->getId() !== null,
            'Candidat' => $Candidat,
        ]);
    }

    /**
     * @Route("candidats/candidat_delete/{id}", name="candidat_delete")
     */
    public function deleteCandidat(Candidats $etu, Request $req)
    {
        //Si le formulaire à été soumis
        if($req->isMethod('POST')){
            // En cas de validation on supprime et on redirige
            if($req->request->has('oui')) {
                $em=$this->getDoctrine()->getManager();
                $em->remove($etu);
                $em->flush();
            }
            // Sinon on redirige simplement
            $this->addFlash('delete','Candidat supprimé');
            return $this->redirectToRoute('research_Candidat');
        } else {
            //Si le formulaire n'a pas été soumis alors on l'affiche
            $title = 'Êtes-vous sûr(e) de vouloir supprimer ce candidat ?';

            $message = 'N°'.$etu->getId().' : '.
                $etu->getPrenom().' '.
                $etu->getNom();


            return $this->render('confirmation.html.twig', [
                'titre' => $title,
                'message' => $message
            ]);
        }

    }

    /**
     * @Route("candidats/candidat_research", name="research_candidat")
     */
    public function researchCandidat(CandidatsRepository $repoE)
    {
        $candidats = $repoE->findAll();

        return $this->render('candidats/research.html.twig', [
            'candidats' => $candidats,
        ]);
    }

    /**
     * @Route("candidat_account", name="candidat_account")
     */
    public function monCompte(UserInterface $Candidat)
    {
        $Candidat = $this->getUser();
        return $this->render('candidats/moncompte.html.twig', [
            'Candidat' => $Candidat,
        ]);
    }

    /**
     * @Route("candidat_account/change_password", name="candidat_change_password")
     */
    public function changePassword(UserInterface $Candidat, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {
        $Candidat = $this->getUser();

        $form = $this->createFormBuilder($Candidat)
            ->add('password', PasswordType::class, array('mapped' => false))
            ->add('new_password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)


            ->getForm();


        $form->handleRequest($request);

        $mdpNonChange = "";

        if($form->isSubmitted() && $form->isValid())
        {
            $match = $encoder->isPasswordValid($Candidat, $form['password']->getData());
            //si password valide
            if($match)
            {
                $hash = $encoder->encodePassword($Candidat, $form['new_password']->getData());
                $Candidat->setPassword($hash);
                $em->persist($Candidat);
                $em->flush();
                $this->addFlash('mdp_change','Votre mot de passe a été modifié avec succès');
                return $this->redirectToRoute('candidat_account');
            }
            else {
                $mdpNonChange = "Le mot de passe entré n'est pas votre mot de passe actuel";
            }
        }



        return $this->render('candidats/changepassword.html.twig', [
            'Candidat' => $Candidat,
            'form_change_password' => $form->createView(),
            'error' => $mdpNonChange,
        ]);
    }



    /**
     * @Route("candidat_account/change_mail", name="change_mail")
     */
    public function changeMail(UserInterface $Candidat, Request $request, ObjectManager $em)
    {
        $Candidat = $this->getUser();

        $form = $this->createFormBuilder($Candidat)
            ->add('mail')


            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($Candidat);
            $em->flush();
            $this->addFlash('mail_change','Votre mail a été modifié avec succès');
            return $this->redirectToRoute('candidat_account');

        }

        return $this->render('candidats/changeemail.html.twig', [
            'Candidat' => $Candidat,
            'form_change_email' => $form->createView()
        ]);
    }

    public function createCandidat($nomCandidat,$prenomCandidat,$mdpCandidat,$mail,$date, Candidatsrepository $repoE, ObjectManager $em, UserPasswordEncoderInterface $encoder, ProfesseursRepository $repoP, SecretaireRepository $repoS){

        $Candidat = new Candidats();

        $mail = strtolower($mail);
        $prenom = ucfirst(strtolower($prenomCandidat));
        $nom = strtoupper($nomCandidat);
        $hash = $encoder->encodePassword($Candidat,$mdpCandidat);

        $Candidat->setMail($mail);
        $Candidat->setNom($nom);
        $Candidat->setPrenom($prenom);
        $Candidat->setPassword($hash);
        $Candidat->setDateNaissance($date);
        $em->persist($Candidat);
        $em->flush();
    }
}

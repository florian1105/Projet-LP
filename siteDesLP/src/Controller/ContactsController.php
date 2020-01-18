<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\Entreprises;
use App\Repository\ContactRepository;
use App\Services\Mailer;
use Symfony\Component\DependencyInjection\Tests\Compiler\C;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ContactsController extends AbstractController
{

    /**
     * @Route("/contact/new", name="contact_add")
     * @Route("/contact/edit/{id}", name="contact_edit")
     */
    public function form( Contacts $contact = null, ContactRepository $repoS, Request $request, ObjectManager $manager,  Mailer $mailer, UserPasswordEncoderInterface $encoder)
    {
        $editMode = true;
        if(!$contact)
        {
            $contact = new Contacts();
            $editMode = false;

            $form = $this->createFormBuilder($contact)
                ->add('nom')
                ->add('prenom')
                ->add('mail')
                ->add('telephone')
                ->add('entreprise', EntityType::class, [
                    'class' => Entreprises::class,
                    'choice_label' => 'Nom',
                ])
                ->getForm();

            $form->handleRequest($request);

            // Encodage du mot de passe

        }
        else if($editMode == true)
        { // Mode edit

            $form = $this->createFormBuilder($contact)
                ->add('nom')
                ->add('prenom')
                ->add('mail')
                ->add('telephone')
                ->add('entreprise', EntityType::class, [
                    'class' => Entreprises::class,
                    'choice_label' => 'Nom',
                ])
                ->getForm();

            $form->handleRequest($request);
        }

        // Réception du form valide -> add/update
        if($form->isSubmitted() && $form->isValid())
        {
            if(!$editMode){
                $this->addFlash('success','Le contact a bien été créé');

                $bodyMail = $mailer->createBodyMail('contacts/mail_new_contact.html.twig', [
                    'contact' => $contact
                ]);
                //envoie du mail
                $mailer->sendMessage('sitedeslp@gmail.com','sitedeslp@gmail.com', 'Inscription d\'un nouveau contact', $bodyMail);
                $this->addFlash('success_contact',"Un mail va vous être envoyé une fois la demande validée");
                $contact->setValide(false);
            }
            else {
                $this->addFlash('success_modifie', 'Le contact a bien été modifié');
                $manager->persist($contact);
                $manager->flush();
                return $this->redirectToRoute("contact_search");
            }

            $manager->persist($contact);
            $manager->flush();
            if($this->getUser()==null){
                return $this->redirectToRoute('entreprises');
            }elseif($this->getUser()->getRoles()==["ROLE_ADMIN"] || $this->getUser()->getRoles()==["ROLE_PROFESSEURRESPONSABLE"]){
                return $this->redirectToRoute("contact_valide",['id'=>$contact->getId()]);
            }

        }


        return $this->render('contacts/index.html.twig', [
            'form' => $form->createView(),
            'editMode' => $contact->getId() !== null,
            'contacts' => $contact
        ]);
    }


    /**
     * @Route("/contact/remove/{id}", name="contact_delete")
     */
    public function delete(Contacts $contacts, Request $req)
    {
        //Si le formulaire à été soumis
        if($req->isMethod('POST'))
        {
            // En cas de validation on supprime et on redirige
            if($req->request->has('oui'))
            {

                $em=$this->getDoctrine()->getManager();
                $em->remove($contacts);
                $em->flush();
                $this->addFlash('delete',"Le contact a été supprimé avec succès");
            }
            else{$this->addFlash('delete',"Aucun contact n'a été supprimé");}
            return $this->redirectToRoute('contact_search_valide');
        }
        else
        {
            //Si le formulaire n'a pas été soumis alors on l'affiche
            $title = 'Êtes-vous sûr(e) de vouloir supprimer ce contact ?';

            $message = 'N°'.$contacts->getId().' : '.
                $contacts->getPrenom().' '.
                $contacts->getNom().')';

            return $this->render('confirmation.html.twig', [
                'titre' => $title,
                'message' => $message
            ]);
        }
    }

    /**
     * @Route("/contact/search", name="contact_search")
     */
    public function search(ContactRepository $repo)
    {
        return $this->render('contacts/research.html.twig', [
            'contacts' => $repo->findAllValide(),
        ]);
    }
    /**
     * @Route("/contact/search_valide", name="contact_search_valide")
     */

    public function search_valide(ContactRepository $repo)
    {
        return $this->render('contacts/attente.html.twig', [
            'contacts' => $repo->findAllUnvalide(),
        ]);

    }

    /**
     * @Route("contact_account", name="contact_account")
     */
    public function monCompte(UserInterface $contact)
    {
        $contact = $this->getUser();
        return $this->render('contacts/moncompte.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("contact_account/change_password", name="contact_change_password")
     */
    public function changePassword(UserInterface $contact, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {
        $contact = $this->getUser();

        $form = $this->createFormBuilder($contact)
            ->add('password', PasswordType::class, array('mapped' => false))
            ->add('new_password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)


            ->getForm();


        $form->handleRequest($request);

        $mdpNonChange = "";

        if($form->isSubmitted() && $form->isValid())
        {
            $match = $encoder->isPasswordValid($contact, $form['password']->getData());
            //si password valide
            if($match)
            {
                $hash = $encoder->encodePassword($contact, $form['new_password']->getData());
                $contact->setPassword($hash);
                $em->persist($contact);
                $em->flush();
                $this->addFlash('mdp_change','Votre mot de passe a été modifié avec succès');
                return $this->redirectToRoute('contact_account');
            }
            else {
                $mdpNonChange = "Le mot de passe entré n'est pas votre mot de passe actuel";
            }
        }



        return $this->render('contacts/changepassword.html.twig', [
            'contact' => $contact,
            'form_change_password' => $form->createView(),
            'error' => $mdpNonChange,
        ]);
    }



    /**
     * @Route("contact_account/change_mail", name="contact_change_mail")
     */
    public function changeMail(UserInterface $contact, Request $request, ObjectManager $em)
    {
        $contact = $this->getUser();

        $form = $this->createFormBuilder($contact)
            ->add('mail')


            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($contact);
            $em->flush();
            $this->addFlash('mail_change','Votre mail a été modifié avec succès');
            return $this->redirectToRoute('contact_account');

        }

        return $this->render('contacts/changeemail.html.twig', [
            'contact' => $contact,
            'form_change_email' => $form->createView()
        ]);
    }


    /**
     * @Route("/contact/valide/{id}", name="contact_valide")
     */
    public function valide(Contacts $contact=null,  ObjectManager $manager, TokengeneratorInterface $tokenGenerator,ContactRepository $repo, Mailer $mailer){

        if(!$contact)
        {
            $contact = new Contacts();
        }

        $entreprise=$contact->getEntreprise();

        if($entreprise->getValide()==false){
            $entreprise=$entreprise->setValide(true);

        }
        $contact=$contact->setValide(true);

        $contact->setPasswordRequestedAt(new \Datetime());

        //On affecte un token et une date de demande de mot de passe(correspond à maintenant)
        $contact->setToken($tokenGenerator->generateToken());
        $manager->persist($entreprise);
        $manager->persist($contact);
        $manager->flush();



        $this->addFlash('success','Le contact a bien été validé');
        $bodyMail = $mailer->createBodyMail('contacts/mail_contact_valide.html.twig', [
            'contact' => $contact
        ]);
        //envoie du mail
        $mailer->sendMessage('sitedeslp@gmail.com',$contact->getMail(), 'Validation de votre demande de contact', $bodyMail);
        $this->addFlash('goodMail',"Un mail a été envoyé au nouveau contact");
        return $this->render('contacts/attente.html.twig', [
            'contacts' => $repo->findAllUnvalide(),

        ]);
        
    }
}

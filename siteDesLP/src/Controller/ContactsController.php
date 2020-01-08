<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\Entreprises;
use App\Repository\ContactRepository;
use Symfony\Component\DependencyInjection\Tests\Compiler\C;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class   ContactsController extends AbstractController
{

    /**
     * @Route("/contact/new", name="contact_add")
     * @Route("/contact/edit/{id}", name="contact_edit")
     */
    public function form( Contacts $contact = null, ContactRepository $repoS, Request $request, ObjectManager $manager)
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
//                $hash = $encoder->encodePassword($secretaire, $secretaire->getNewPassword());
//                $secretaire->setPassword($hash);
                $this->addFlash('success','Le contact a bien été créé');
            }
            else{$this->addFlash('success_modifie','Le contact a bien été modifié');}
            $contact->setValide(false);
            $manager->persist($contact);
            $manager->flush();
            return $this->redirectToRoute('contact_search');
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
            return $this->redirectToRoute('contact_search');
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
     * @Route("/contact/valide/{id}", name="contact_valide")
     */
    public function valide(Contacts $contact=null,  ObjectManager $manager,ContactRepository $repo){
        if(!$contact)
        {
            $contact = new Contacts();
        }

        $entreprise=$contact->getEntreprise();
        if($contact->getEntreprise()->getValide()==false){
            $entreprise->setValide(true);
            $entreprise->setContactEntreprise($contact);
        }

        $contact->setValide(true);
        $manager->persist($contact);
        $manager->persist($entreprise);
        $manager->flush();
        $this->addFlash('success','Le contact a bien été validé');
        return $this->render('contacts/attente.html.twig', [
            'contacts' => $repo->findAllUnvalide(),

        ]);
        
    }
}

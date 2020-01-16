<?php


namespace App\Controller;


use App\Entity\Entreprises;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\EntreprisesRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EntreprisesController extends AbstractController
{
    /**
     * @Route("/entreprises", name="entreprises")
     */
    public function page()
    {
        return $this->render('entreprises/page.html.twig');      
    }
    
    /**
     * @Route("/entreprise/new", name="entreprise_create")
     * @Route("/entreprise/{id}/edit", name="entreprise_edit")
     */
    public function form(Entreprises $entreprise = null, EntreprisesRepository $repoE, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {
        $editMode = true;

        if(!$entreprise)
        {
            $entreprise = new Entreprises();
            $editMode = false;
        }

        $form = $this->createFormBuilder($entreprise)
            ->add('nom')
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) 
        {
            if ($editMode == false) 
            {
                $this->addFlash('success', 'L\'entreprise a bien été créé');
            } 
            else 
            {
                $this->addFlash('success_modifie', 'Les changements on biens été pris en compte');
            }

            $nom = strtoupper($form['nom']->getData());
            $entreprise->setNom($nom);

            if($this->getUser()->getRoles()=="ROLE_ADMIN" || $this->getUser()->getRoles()=="ROLE_PROFRESPONSABLE"){
               $entreprise->setValide(true);
            }else {
                $entreprise->setValide(false);
            }
            $em->persist($entreprise);
            $em->flush();


            return $this->redirectToRoute('research_entreprise');

        }

        return $this->render('entreprises/index.html.twig', [
            'form_create_entreprise' => $form->createView(),
            'editMode' => $entreprise->getId() !== null,
            'entreprise' => $entreprise,
        ]);
    }

    /**
     * @Route("/entreprises/entreprise_delete/{id}", name="entreprise_delete")
     */
    public function deleteEntreprise(Entreprises $ent, Request $req)
    {
        //Si le formulaire à été soumis
        if($req->isMethod('POST')){
            // En cas de validation on supprime et on redirige
            if($req->request->has('oui')) {
                $em=$this->getDoctrine()->getManager();
                $em->remove($ent);
                $em->flush();
            }
            // Sinon on redirige simplement
            $this->addFlash('delete','entreprise supprimé');
            return $this->redirectToRoute('research_entreprise');
        } else {
            //Si le formulaire n'a pas été soumis alors on l'affiche
            $title = 'Êtes-vous sûr(e) de vouloir supprimer cette entreprise ?';

            $message = 'Entreprise : '.$ent->getNom() ;


            return $this->render('confirmation.html.twig', [
                'titre' => $title,
                'message' => $message
            ]);
        }

    }


    /**
     * @Route("/entreprises/entreprise_research", name="research_entreprise")
     */
    public function researchentreprise(EntreprisesRepository $repoE)
    {
        //if($this->getUser()->getRoles()[0] == "ROLE_PROFESSEURRESPONSABLE") //Si l'utilisateur est un professeur responsable
        //{
            $entreprises = $repoE->findAllValide();
            return $this->render('entreprises/research.html.twig', [
                'entreprises' => $entreprises,
            ]);
       // }else $this->redirectToRoute('entreprise_create');

    }
    /**
     * @Route("/entreprise/search_valide", name="entreprise_search_valide")
     */

    public function search_valide(EntreprisesRepository $repo)
    {
        return $this->render('entreprises/attente.html.twig', [
            'entreprises' => $repo->findAllUnvalide(),
        ]);

    }

    /**
     * @Route("/entreprise/valide/{id}", name="entreprise_valide")
     */
    public function valide(Entreprises $entreprise=null,  ObjectManager $manager,EntreprisesRepository $repo){
        if(!$entreprise)
        {
            $entreprise = new Entreprises();
        }


        if($entreprise->getValide()==false){
            $entreprise->setValide(true);
        }

        $manager->persist($entreprise);
        $manager->flush();
        $this->addFlash('success','L\'entreprise a bien été validé');
        return $this->render('entreprises/attente.html.twig', [
            'entreprises' => $repo->findAllUnvalide(),

        ]);

    }


}
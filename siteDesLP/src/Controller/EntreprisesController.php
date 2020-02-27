<?php


namespace App\Controller;


use App\Entity\Entreprises;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\EntreprisesRepository;
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
     * @Route("/entreprise/nouveau", name="entreprise_nouveau")
     * @Route("/entreprise/modifier/{id}", name="entreprise_modifier")
     */
    public function form(Entreprises $entreprise = null, EntreprisesRepository $repoE, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
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

            if($this->getUser()==null){
                $entreprise->setValide(false);
            }else {
                $entreprise->setValide(true);
            }
            $em->persist($entreprise);
            $em->flush();


            return $this->redirectToRoute('contact_nouveau');

        }

        return $this->render('entreprises/index.html.twig', [
            'form_create_entreprise' => $form->createView(),
            'editMode' => $entreprise->getId() !== null,
            'entreprise' => $entreprise,
        ]);
    }



    /**
     * @Route("/entreprises/rechercher", name="entreprise_rechercher")
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
     * @Route("/entreprise/rechercher_invalide", name="entreprise_rechercher_invalide")
     */

    public function search_valide(EntreprisesRepository $repo)
    {
        return $this->render('entreprises/attente.html.twig', [
            'entreprises' => $repo->findAllUnvalide(),
        ]);

    }

    /**
     * @Route("/entreprise/valider/{id}", name="entreprise_valider")
     */
    public function valide(Entreprises $entreprise=null,  EntityManagerInterface $manager,EntreprisesRepository $repo){
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
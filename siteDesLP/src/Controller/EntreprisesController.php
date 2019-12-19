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

        if(!$editMode) {

            $form = $this->createFormBuilder($entreprise)
                ->add('nom')
                ->add('adresseMail')
                ->add('password')
                ->getForm();

            $form->handleRequest($request);


            $nom = strtoupper($form['nom']->getData());
            $adresseMail = strtolower($form['adresseMail']->getData());
            $mdp = strtolower($form['password']->getData());
            $entreprise->setNom($nom);
            $entreprise->setAdresseMail($adresseMail);
            $entreprise->setPassword($mdp);


        }else {
            $form = $this->createFormBuilder($entreprise)
                ->add('nom')
                ->add('adresseMail')
                ->add('password')
                ->getForm();
            $form->handleRequest($request);
            $adresseMail = strtolower($form['adresseMail']->getData());
            $mdp = ucfirst(strtolower($form['password']->getData()));
            $nom = strtoupper($form['nom']->getData());

            $entreprise->setAdresseMail($adresseMail);
            $entreprise->setNom($nom);
            $entreprise->setPassword($mdp);

            return $this->render('entreprises/index.html.twig', [
                'form_create_entreprise' => $form->createView(),
                'editMode' => $entreprise->getId() !== null,
                'etudiant' => $entreprise,
            ]);

        }

        if($form->isSubmitted() && $form->isValid()) {
            if ($editMode == false) {
                $hash = $encoder->encodePassword($entreprise, $entreprise->getPassword());
                $entreprise->setPassword($hash);
                $this->addFlash('success', 'l\'étudiant a bien été créé');
            } else {
                $this->addFlash('success_modifie', 'les changements on biens été pris en compte');
            }
            $em->persist($entreprise);
            $em->flush();


            return $this->redirectToRoute('research_entreprise');
        }
    }


}
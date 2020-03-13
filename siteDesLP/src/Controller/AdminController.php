<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
  
    /**
     * @Route("/admin/changer_mdp", name="admin_changer_mdp")
     */
    public function changePassword(UserInterface $admin, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $admin = $this->getUser();

        $form = $this->createFormBuilder($admin)
            ->add('password', PasswordType::class, array('mapped' => false))
            ->add('new_password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)


            ->getForm();


        $form->handleRequest($request);

        $mdpNonChange = "";

        if($form->isSubmitted() && $form->isValid())
        {
            $match = $encoder->isPasswordValid($admin, $form['password']->getData());
            //si password valide
            if($match)
            {
                $hash = $encoder->encodePassword($admin, $form['new_password']->getData());
                $admin->setPassword($hash);
                $em->persist($admin);
                $em->flush();
                $this->addFlash('mdp_change','Votre mot de passe a été modifié avec succès');
                return $this->redirectToRoute('home');
            }
            else {
                $mdpNonChange = "Le mot de passe entré n'est pas votre mot de passe actuel";
            }
        }



        return $this->render('admin/changepassword.html.twig', [
            'admin' => $admin,
            'form_change_password' => $form->createView(),
            'error' => $mdpNonChange,
        ]);
    }
}

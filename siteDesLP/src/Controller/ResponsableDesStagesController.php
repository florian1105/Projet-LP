<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ResponsableDesStages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EtudiantsRepository;
use App\Repository\SecretaireRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\ResponsableDesStagesRepository;




class ResponsableDesStagesController extends AbstractController
{
  /**
  * @Route("/responsable_des_stages/nouveau", name="responsable_des_stages_nouveau")
  * @Route("/responsable_des_stages/modifier/{id}", name="responsable_des_stages_modifier")
  */
  public function formulaire(ResponsableDesStages $responsableDesStages = null, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,Request $request, ResponsableDesStagesRepository $repoR, EtudiantsRepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS)
  {
    $editMode = true;
    if(!$responsableDesStages)
    {
      $responsableDesStages = new ResponsableDesStages();
      $editMode = false;

      $form = $this->createFormBuilder($responsableDesStages)
      ->add('nom')
      ->add('prenom')
      ->add('new_password', PasswordType::class,
      [
        'attr' => ['maxlength' => '64']
      ])
      ->add('confirmPassword', PasswordType::class,
      [
        'attr' => ['maxlength' => '64'],
      ])
      ->getForm();

      $form->handleRequest($request);


      // Gestion des champs générés
      $prenom = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(trim($responsableDesStages->getPrenom())));

      $nom = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(trim($responsableDesStages->getNom())));

      $mailAcademique = $prenom.".".$nom;

      $prenom = substr($prenom, 0,1);
      $login = strtolower($nom).$prenom;

      $i = "";
      $j = "";

      while($repoE->findBy(['login' => $login.$i]) || $repoP->findBy(['login' => $login.$i]) || $repoS->findBy(['login' => $login.$i]) || $repoR->findBy(['login' => $login.$i]))
      {
        if($i == "") $i = 0;
        $i++;
      }

      while($repoS->findBy(['mailAcademique' => $mailAcademique.$j."@umontpellier.fr"]) || $repoP->findBy(['mailAcademique' => $mailAcademique.$j."@umontpellier.fr"]) || $repoR->findBy(['mailAcademique' => $mailAcademique.$j."@umontpellier.fr"]))
      {
        if($j == "") $j = 0;
        $j++;
      }

      $responsableDesStages->setLogin($login.$i);
      $responsableDesStages->setMailAcademique($mailAcademique.$j."@umontpellier.fr");

      $prenom = ucfirst(strtolower($form['prenom']->getData()));
      $nom = strtoupper($form['nom']->getData());

      $responsableDesStages->setNom($nom);
      $responsableDesStages->setPrenom($prenom);

      $editmode = 0;
    }
    else if($editMode == true)
    { // Mode edit

      $form = $this->createFormBuilder($responsableDesStages)
      ->add('nom')
      ->add('prenom')
      ->add('login')
      ->add('mailAcademique')

      ->getForm();

      $form->handleRequest($request);
    }

    // Réception du form valide -> add/update
    if($form->isSubmitted() && $form->isValid())
    {
      if(!$editMode)
      {
        $hash = $encoder->encodePassword($responsableDesStages, $responsableDesStages->getNewPassword());
        $responsableDesStages->setPassword($hash);
        $this->addFlash('success','La/Le responsable des stages a bien été créée');
      }
      else{$this->addFlash('success_modifie','La/Le responsable des stages a bien été modifié');}

      $manager->persist($responsableDesStages);
      $manager->flush();
      return $this->redirectToRoute('responsable_des_stages_rechercher');
    }


    return $this->render('responsable_des_stages/index.html.twig', [
      'form' => $form->createView(),
      'editMode' => $responsableDesStages->getId() !== null,
      'responsable_des_stages' => $responsableDesStages
    ]);
  }

  /**
   * @Route("/responsable_des_stages/rechercher", name="responsable_des_stages_rechercher")
   */
  public function rechercher(ResponsableDesStagesRepository $repoR)
  {
      return $this->render('responsable_des_stages/research.html.twig', [
          'responsables_des_stages' => $repoR->findAll(),
      ]);
  }

  /**
   * @Route("/responsable_des_stages/supprimer/{id}", name="responsable_des_stages_supprimer")
   */
  public function supprimer(ResponsableDesStages $responsableDesStages, Request $req)
  {
      //Si le formulaire à été soumis
      if($req->isMethod('POST'))
      {
          if($req->request->has('oui'))
          {

              $em=$this->getDoctrine()->getManager();
              $em->remove($responsableDesStages);
              $em->flush();
              $this->addFlash('supprimer',"La/Le responsable des stages a été supprimé avec succès");
          }
          else
          {
            $this->addFlash('pasSupprimer',"Aucun(e) responsable des stages n'a été supprimé");
          }
          return $this->redirectToRoute('responsable_des_stages_rechercher');
      }
      else
      {
          //Si le formulaire n'a pas été soumis alors on l'affiche
          $title = 'Êtes-vous sûr(e) de vouloir supprimer ce/cette responsable des stages ?';

          $message = 'N°'.$responsableDesStages->getId().' : '.
              $responsableDesStages->getPrenom().' '.
              $responsableDesStages->getNom(). ' ('.
              $responsableDesStages->getLogin().')';

          return $this->render('confirmation.html.twig', [
              'titre' => $title,
              'message' => $message
          ]);
      }
  }

  /**
   * @Route("responsable_des_stages/compte", name="responsable_des_stages_compte")
   */
  public function monCompte(UserInterface $responsableDesStages)
  {
      $responsableDesStages = $this->getUser();

      return $this->render('responsable_des_stages/moncompte.html.twig', [
          'responsable_des_stages' => $responsableDesStages,
      ]);
  }

  /**
	 * @Route("responsable_des_stages/compte/changer_mdp", name="responsable_des_stages_changer_mdp")
	 */
	public function changerMdp(UserInterface $responsableDesStages, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
	{
			$responsableDesStages = $this->getUser();

			$form = $this->createFormBuilder($responsableDesStages)
			->add('password', PasswordType::class, array('mapped' => false))
			->add('new_password', PasswordType::class)
			->add('confirmPassword', PasswordType::class)


			->getForm();


			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid())
			{
				$match = $encoder->isPasswordValid($responsableDesStages, $form['password']->getData());
				//si password valide
				if($match)
				{
					$hash = $encoder->encodePassword($responsableDesStages, $form['new_password']->getData());
					$responsableDesStages->setPassword($hash);
					$em->persist($responsableDesStages);
					$em->flush();
					$this->addFlash('mdp_change','Votre mot de passe a été modifié avec succès');
					return $this->redirectToRoute('responsable_des_stages_compte');
				}
				else
        {
          $this->addFlash('mdp_non_change', "Le mot de passe entré n'est pas votre mot de passe actuel");
				}
			}



			return $this->render('responsable_des_stages/changepassword.html.twig', [
					'responsable_des_stages' => $responsableDesStages,
					'form_change_password' => $form->createView(),
			]);
	}






}

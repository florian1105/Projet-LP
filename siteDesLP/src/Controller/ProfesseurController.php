<?php
namespace App\Controller;

/** Symfony **/
use App\Entity\Classes;
use App\Entity\Professeurs;
use App\Repository\ClassesRepository;
use App\Repository\EtudiantsRepository;
use App\Repository\ProfesseursRepository;
use App\Repository\SecretaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/** Doctrine **/
use Symfony\Component\Security\Core\User\UserInterface;

/** Propriétaire **/
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class ProfesseurController extends AbstractController
{
    /**
     * @Route("/professeur/nouveau", name="prof_ajouter")
     * @Route("/professeur/modifier/{id}", name="prof_modifier")
     */
    public function form(Professeurs $prof = null, ProfesseursRepository $repoP, SecretaireRepository $repoS, EtudiantsRepository $repoE, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
		$editMode = true;
		if(!$prof)
		{
			$editMode = false;
			$prof = new Professeurs();

			$form = $this->createFormBuilder($prof)
				->add('nomProfesseur')
			    ->add('prenomProfesseur')
				->add('new_password', PasswordType::class,
				[
					'attr' => ['maxlength' => '64']
				])
				->add('confirm_password', PasswordType::class,
				[
					'attr' => ['maxlength' => '64'],
				])
				->add('classes', EntityType::class,
				[
					'class' => Classes::class,
					'choice_label' => 'nomClasse',
					'label' => 'Classes du professeur',
					'expanded' => true,
					'multiple' => true,
					'mapped' => false, //décoché par défaut
					'by_reference' => false,
					/*'group_by' => function($choice, $key, $val) {
						return strtoupper(substr($choice->getNomClasse(), 0, 1));
					},*/
				])
		 	->getForm();

	        $form->handleRequest($request);

			// Ajouts des classes d'un prof
			$classesProf = $form['classes']->getData();
			if ($classesProf)
			{
				for ($i=0; $i < sizeof($classesProf); $i++)
				{
					$classe=$classesProf[$i];
					$prof->getClasses()->add($classe);
				}
			}

	        // Gestion des champs générés
			$prenom = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(trim($prof->getPrenomProfesseur())));

			$nom = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower(trim($prof->getNomProfesseur())));

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

			$prof->setLogin($login.$i);
			$prof->setMailAcademique($mailAcademique.$j."@umontpellier.fr");

			$prenom = ucfirst(strtolower($form['prenomProfesseur']->getData()));
			$nom = strtoupper($form['nomProfesseur']->getData());

			$prof->setNomProfesseur($nom);
			$prof->setprenomProfesseur($prenom);

			// Encodage du mot de passe


		}
		else
		{ // Mode edit

			$form = $this->createFormBuilder($prof)
				->add('nomProfesseur')
			    ->add('prenomProfesseur')
			    ->add('login')
      			->add('mailAcademique')
				->add('classes', EntityType::class,
				[
			        'class' => Classes::class,
			        'choice_label' => 'nomClasse',
			        'label' => 'Classes du professeur',
			        'expanded' => true,
			        'multiple' => true,
			        'mapped' => true,
			        //'by_reference' => false,
			    ])

			    ->getForm();

	        $form->handleRequest($request);
		}

		// Réception du form valide -> add/update
		if($form->isSubmitted() && $form->isValid())
		{

			if($editMode == false)
			{
				$hash = $encoder->encodePassword($prof, $prof->getNewPassword());
				$prof->setPassword($hash);
			}

			$manager->persist($prof);
			$manager->flush();
			if($editMode == true) $this->addFlash('success_modifie','Le professeur a bien été modifié');
			else $this->addFlash('success_modifie','Professeur créé avec succès');
			return $this->redirectToRoute('prof_rechercher');
		}


        return $this->render('professeur/index.html.twig', [
			'form' => $form->createView(),
			'editMode' => $prof->getId() !== null,
			'prof' => $prof/*,
			'classes' => $classes*/
        ]);
    }

    /**
     * @Route("/professeur/supprimer/{id}", name="prof_supprimer")
     */
    public function delete(Professeurs $prof, Request $req)
    {
    	//Si le formulaire à été soumis
		if($req->isMethod('POST'))
		{
    		// En cas de validation on supprime et on redirige
			if($req->request->has('oui'))
			{
				if($prof->getClasseResponsable() != null)
				{
          			$this->addFlash('notDelete',"Ce professeur ne peut pas être supprimé car il est responsable d'une classe");
					return $this->redirectToRoute('prof_rechercher');
				}
				else
				{
					$em=$this->getDoctrine()->getManager();
					$em->remove($prof);
					$em->flush();
    			$this->addFlash('delete',"Ce professeur a été supprimé avec succès");
					return $this->redirectToRoute('prof_rechercher');
				}
			}
      else
      {
        return $this->redirectToRoute('prof_rechercher');
      }

		}
		else
		{
			//Si le formulaire n'a pas été soumis alors on l'affiche
			$title = 'Êtes-vous sûr(e) de vouloir supprimer ce professeur ?';

			$message = $prof->getPrenomProfesseur().' '.$prof->getNomProfesseur().
				' ('.$prof->getLogin().')';

    		return $this->render('confirmation.html.twig', [
					'titre' => $title,
					'message' => $message
	        	]);
	    }
    }

    /**
     * @Route("/professeur/rechercher", name="prof_rechercher")
     */
    public function search(ProfesseursRepository $repo)
    {
        return $this->render('professeur/research.html.twig', [
            'profs' => $repo->findAll(),
        ]);
	}

	 /**
     * @Route("/professeur_compte", name="professeur_compte")
     */
    public function monCompte(UserInterface $prof)
    {
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
		{
			// Sinon on déclenche une exception « Accès interdit »
			throw new AccessDeniedException("L'administrateur n'a pas accès à ceci.");
		}

		$prof = $this->getUser();

        return $this->render('professeur/moncompte.html.twig', [
            'prof' => $prof,
        ]);
	}

	/**
     * @Route("professeur_compte/changer_mdp", name="professeur_changer_mdr")
     */
    public function changePassword(UserInterface $prof, Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder)
    {

        $prof = $this->getUser();

        $form = $this->createFormBuilder($prof)
        ->add('password', PasswordType::class, array('mapped' => false))
        ->add('new_password', PasswordType::class)
        ->add('confirm_password', PasswordType::class)


        ->getForm();


        $form->handleRequest($request);

        $mdpChange = "";
        $mdpNonChange = "";

        if($form->isSubmitted() && $form->isValid())
        {
          $match = $encoder->isPasswordValid($prof, $form['password']->getData());
          //si password valide
          if($match)
          {
            $hash = $encoder->encodePassword($prof, $form['new_password']->getData());
            $prof->setPassword($hash);
            $em->persist($prof);
            $em->flush();
            $this->addFlash('success', 'Mot de passe modifié ! ');
            return $this->redirectToRoute('professeur_compte');
          }

          else {
            $mdpNonChange = "Le mot de passe entré n'est pas votre mot de passe actuel";
          }

        }



        return $this->render('professeur/changepassword.html.twig', [
            'prof' => $prof,
            'form_change_password' => $form->createView(),
            'error' => $mdpNonChange,
        ]);
    }


}

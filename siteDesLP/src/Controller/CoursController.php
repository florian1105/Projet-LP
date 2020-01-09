<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Professeurs;
use App\Entity\Etudiants;
use App\Entity\Cours;
use App\Entity\Classes;
use Doctrine\Common\Persistence\ObjectManager;

use App\Repository\CoursRepository;

class CoursController extends AbstractController
{
    /**
     * @Route("/ent/gestion", name="cours_gest")
     */
    public function gererCours(Request $request)
    {
    	/* Récupère le prof connecté */
		$prof = $this->getUser();

		if(! $prof instanceof Professeurs)
			return $this->redirectToRoute('connexion');

       	/* Formulaire d'ajout d'un dossier
       	   de cours */
		$cours = new Cours();

		$form = $this->createFormBuilder($cours)
        ->add('nom')
        ->add('classes', EntityType::class,
        [
          'class' => Classes::class,
          'choice_label' => 'nomClasse',
          'label' => 'Classes de l\'article',
          'expanded' => true,
          'multiple' => true,
          'mapped' => true,
          'by_reference' => false,
        ])
        ->add('coursParent', EntityType::class,
		[
			'class' => Cours::class,
			'choice_label' => 'id',
			'label' => 'Dossier de cours parent',
			'expanded' => false,
			'multiple' => false,
			'required' => false,
		])

	 	->getForm();

        $form->handleRequest($request);

		// Réception du form valide -> add/update
		if($form->isSubmitted() && $form->isValid())
		{
				$cours->setProf($prof);

				$manager = $this->getDoctrine()->getManager();
				$cours->setVisible(true);
				$manager->persist($cours);
				$manager->flush();

				// Réinitialisation du formulaire
				unset($cours);
				unset($form);
				$cours = new Cours();
				$form = $this->createFormBuilder($cours)
		        ->add('nom')
		        ->add('classes', EntityType::class,
		        [
		          'class' => Classes::class,
		          'choice_label' => 'nomClasse',
		          'label' => 'Classes de l\'article',
		          'expanded' => true,
		          'multiple' => true,
		          'mapped' => true,
		          'by_reference' => false,
		        ])
		        ->add('coursParent', EntityType::class,
				[
					'class' => Cours::class,
					'choice_label' => 'id',
					'label' => 'Dossier de cours parent',
					'expanded' => false,
					'multiple' => false,
					'required' => false,
				])
			 	->getForm();
		}

    	/* Récupère ses dossiers de cours */
    	$cours = $prof->getDossiersCours();

		/* Va chercher les dossiers racines
		   (sans parent) */
    	// -> faire une requete dans le modele
		$dossiersPrincipaux = [];
    	foreach ($cours as $dossier) {
	    	if($dossier->getCoursParent() == null)
    			$dossiersPrincipaux[] = $dossier;
       	}

    	/* Affichage */
		return $this->render('cours/gestion.html.twig', [
            'dossiersRacines' => $dossiersPrincipaux,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ent/cours", name="cours_affi")
     */
    public function afficherCours(CoursRepository $coursRepo)
    {
    	/* Récuperation de l'utilisateur connecté */
		$etu = $this->getUser();

		/* Verification que ce soit un etudiant */
		if(! $etu instanceof Etudiants)
			return $this->redirectToRoute('connexion');

    	/* Récupère la classe de l'étudiant */
    	$classe = $etu->getClasseEtudiant();

		/* Récupère l'arborescence */
    	$results = $coursRepo->getTreeClasse($classe);

    	// debug
    	foreach ($results as $cours) {
    		$id      = $cours->getId();
    		$nom     = $cours->getNom();
    		$prof    = $cours->getProf();
    		$parent  = $cours->getCoursParent();
    		$visible = $cours->getVisible();
    		
    		if($parent != null)
    			$parent = $parent->getNom();
    		else
    			$parent = 'racine';

    		if($prof != null)
    			$prof = $prof->getNomProfesseur();
    		else
    			$prof = 'null';
    		
    		print_r('id:' . $id . ' | nom:' . $nom . ' | parent:' . $parent . ' | prof:' . $prof . ' | visible:' . $visible . '<br>');
    	}
    	// fin debug

    	/* Affichage */
		return $this->render('cours/affichage.html.twig', [
            'data' => $results,
        ]);
    }

    /**
     * @Route("/ent/cours/delete/{id}", name="cours_delete")
     */
    public function supprimeCours(Cours $cours, Request $req)
    {
		/* Récupère le prof connecté */
		$prof = $this->getUser();

		if(! $prof instanceof Professeurs)
			return $this->redirectToRoute('connexion');

		//Si le formulaire à été soumis
		if($req->isMethod('POST'))
		{
    		// En cas de validation on supprime et on redirige
			if($req->request->has('oui'))
			{
				$em=$this->getDoctrine()->getManager();
				$em->remove($cours);
				$em->flush();
      			$this->addFlash('delete',"Ce cours et tout ce qu'il contenais a été supprimé avec succès");
			}
			return $this->redirectToRoute('cours_gest');
		} else {
			//Si le formulaire n'a pas été soumis alors on l'affiche
			$title = 'Êtes-vous sûr(e) de vouloir supprimer ce dossier et tout ce qu\'il contient ?';

			$message = 'Le dossier "'.$cours->getNom().'" sera supprimé de manière irréversible.';

    		return $this->render('confirmation.html.twig', [
					'titre' => $title,
					'message' => $message
	        	]);
	    }
    }

    /**
     * @Route("/ent/cours/edit/{id}", name="dossier_edit")
     */
    public function edit(Request $request, Cours $cours, ObjectManager $em)
    {
        $form = $this->createFormBuilder($cours)
        ->add('nom')
        ->add('visible')
        ->add('classes', EntityType::class,
        [
          'class' => Classes::class,
          'choice_label' => 'nomClasse',
          'label' => 'Classes de l\'article',
          'expanded' => true,
          'multiple' => true,
          'mapped' => true,
          'by_reference' => false,
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          $em->persist($cours);
          $em->flush();
          $this->addFlash('editTrue','Le dossier a été modifié avec succès');

          return $this->redirectToRoute('cours_gest');
        }

        return $this->render('cours/edit.html.twig', [
          'form' => $form->createView(),
          'cours' => $cours
        ]);

    }
}
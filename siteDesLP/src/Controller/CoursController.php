<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProfesseursRepository;
use App\Repository\ClassesRepository;

class CoursController extends AbstractController
{
    /**
     * @Route("/ent/gestion", name="cours_gest")
     */
    public function gererCours(ProfesseursRepository $repoProfs)
    {
    	/* Récupère le prof connecté */
    	$prof = "palleja";
    	$prof = $repoProfs->findByNomProfesseur($prof)[0];

    	/* Récupère ses dossiers de cours */
    	$cours = $prof->getDossiersCours();
		
		// Va chercher les dossiers sans parent
    	// -> les dossiers racines
    	foreach ($cours as $dossier) {
	    	if($dossier->getCoursParent() == null)
    			$dossiersPrincipaux[] = $dossier;
       	}

    	/* Affichage */
		return $this->render('cours/gestion.html.twig', [
            'dossiersRacines' => $dossiersPrincipaux,
        ]);
    }


    /**
     * @Route("/ent/cours", name="cours_affi")
     */
    public function afficherCours(ClassesRepository $repoClasses)
    {
    	/* Récuperation de l'étudiant connecté */
    	$etu = "schallerm";

    	/* Va chercher la classe de l'étudiant */
    	$classe = "apidae";//"dgsdg";
    	$classe = $repoClasses->findByNomClasse($classe);

    	/* Va chercher les dossiers de cours
    	   auquels la classe à accès */
    	$cours = $classe[0]->getCours();

    	// Crée l'arborescence des dossiers accessibles
    	$dossiers = [];
    	foreach ($cours as $dossier) {
			//Vérifie si parent est déjà dans la liste
			$skip = false;
			foreach ($dossiers as $parent) {
				if($dossier->getCoursParent() !== null)
					if($dossier->getCoursParent()->getId()==$parent->getId()){
						$parent->addCoursEnfant($dossier);
						//$dossiers[] = $dossier;
						$skip = true;
					}
			}

			if(!$skip) {
				// Copie des enfants
				$listeEnfants = $dossier->getCoursEnfants();

				//Vidage des enfants
				/*foreach ($dossier->getCoursEnfants() as $enfant) {
					$dossier->removeCoursEnfant($enfant);
				}*/

				foreach ($listeEnfants as $enfant) {
					foreach ($cours as $dir) {
						// Si enfant est dans liste d origine
						if($enfant->getId() == $dir->getId()){
							// Enleve enfant de la liste finale si il a deja ete ajouté
							$id = array_search($enfant, $dossiers);
							if($id !== false) 
								array_splice($dossiers, $id);
							
							// Le déplace dans son parent
							$dossier->addCoursEnfant($enfant);
							//$parentID = array_search($dossier, $dossiers);
							//$dossiers[$parentID]->addCoursEnfant($enfant);

							// Supprime de la liste de cours
							$dir = null;
						}
					}
				}

				//Ajout
				$dossiers[] = $dossier;
			}
		}

       	//var_dump($dossiers);

    	/* Affichage */
		return $this->render('cours/affichage.html.twig', [
            'data' => $dossiers,
        ]);
    }
}


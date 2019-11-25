<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Fichiers;
use App\Form\FichiersType;

class FichiersController extends AbstractController
{
    /**
     * @Route("/ent/send", name="upload")
     */
    public function index(Request $request)
    {
    	// Fichier envoyé par l'utilisateur
    	$upload = new Fichiers();
    	
    	// Création du formulaire
    	$form = $this->createForm(FichiersType::class, $upload);
    	$form->handleRequest($request);

    	// Réception du formulaire
    	if ($form->isSubmitted() && $form->isValid()) {
    		// Création du fichier local
    		$fichier = $upload->getFilePath();
    		
    		// Vérification du nom du fichier
    		$nomfichier = $verifFileName($fichier->getClientOriginalName());

    		// Emplacement dans l'arborescence
    		$cheminFichier = 'tests_upload/';
    		
    		// Déplacement dans son répertorie
    		$fichier->move($this->getParameter('upload_directory').$cheminFichier, $nomfichier);

    		// Màj des données de la bd
    		$upload->setFilePath($cheminFichier.$nomfichier);

    		//return $this->redirectToRoute('upload');
    	}

        return $this->render('fichiers/index.html.twig',[
        	'form' => $form->createView(),
        ]);
    }
}

/* Verifie par sécurité le nom du ficheir transmit */
function verifFileName($filename) {
	$filename = pathinfo($filename, PATHINFO_FILENAME);
	$filename = @stripslashes(@strip_tags($filename));
	if ($fichier->guessExtension())
		$filename .= '.'.$fichier->guessExtension();
	else 
		$filename .= '.unknown';
	return $filename;
}
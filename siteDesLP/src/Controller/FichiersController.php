<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Filesystem\Filesystem;


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
    		$fichier = $upload->getEmplacement();
    		// Vérification du nom du fichier
    		$nomfichier = verifFileName($upload->getClientOriginalName());

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

    /**
     * @Route("/ent/dl/{id}", name="fichier_dl")
     */
    public function download(Fichiers $fichier, Request $req, Filesystem $fs)
    {
        /* Tester si l'utilisateur
           à accès à la ressource */
        
        // Repertoire des fichiers
        $cheminFichier = $this->getParameter('upload_directory').'tests_upload/';

        // Nom du fichier
        $nomfichier = $fichier->getEmplacement();

        // Emplacement du fichier sur le serveur
        $cheminFichier = $cheminFichier.$nomfichier;

        // Test si le fichier existe
        if($fs->exists($cheminFichier))
            // Renvoie le fichier pour le téléchargement
            return new BinaryFileResponse($cheminFichier);

        // Sinon le fichier n'existe pas

        // Message d'erreur (a flasher)
        $msg = "Le fichier demandé n'existe plus";

        // Retourne à la page précedente
        $referer = $req->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/ent/fichier/{id}/delete", name="fichier_delete")
     */
    public function supprimeFichier(Fichiers $fichier, Request $req)
    {
        /* Récupère le prof connecté */
        $prof = $this->getUser();

        $createur = $fichier->getCours()->getProf();

        if($prof !== $createur)
        {
            $flashMsg = 'Seul le professeur '.$createur->getNomProfesseur().' '.$createur->getPrenomProfesseur().' peut supprimer son fichier.';
            return $this->redirectToRoute('connexion');
        }

        //Si le formulaire à été soumis
        if($req->isMethod('POST'))
        {
            // En cas de validation on supprime et on redirige
            if($req->request->has('oui'))
            {
                $em=$this->getDoctrine()->getManager();
                $em->remove($fichier);
                $em->flush();
                $this->addFlash('delete','Le fichier "'.$fichier->getNom().'" a été supprimé avec succès');
            }
            return $this->redirectToRoute('cours_gest');
        } else {
            //Si le formulaire n'a pas été soumis alors on l'affiche
            $title = 'Êtes-vous sûr(e) de vouloir supprimer ce fichier ?';

            $message = 'Le fichier "'.$fichier->getNom().'" sera supprimé de manière irréversible.';

            return $this->render('confirmation.html.twig', [
                    'titre' => $title,
                    'message' => $message
                ]);
        }
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

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


use App\Entity\Cours;
use App\Entity\Fichiers;
use App\Form\FichiersType;
use App\Repository\CoursRepository;

class FichiersController extends AbstractController
{
  /**
  * @Route("/ent/send/{cours}", name="upload")
  */
    public function upload(Cours $cours, Request $request, ObjectManager $em, CoursRepository $repoC)
    {
        // Contrôle des droits d'accès
        // Récupère le prof connecté
        $prof = $this->getUser();

        // Récupère le prof du fichier
        $createur = $cours->getProf();

        if($prof !== $createur)
        {
            // TODO Message d'erreur
            $flashMsg = 'Seul le professeur '.$createur->getNomProfesseur().' '.$createur->getPrenomProfesseur().' peut inserer des fichiers dans ce dossier.';
            return $this->redirectToRoute('connexion');
        }

        // Fichier envoyé par l'utilisateur
        $upload = new Fichiers();

        // Création du formulaire
        $form = $this->createForm(FichiersType::class, $upload);
        $form->handleRequest($request);

        // Réception du formulaire
        if ($form->isSubmitted() && $form->isValid())
        {
            // Création du fichier local UploadedFile
            $userFiles = $form['formFichiers']->getData();

            foreach ($userFiles as $fichier) {

                // Récuperation du nom du fichier original
                $nomfichier = $fichier->getClientOriginalName();

                /* Verifie que le dossier ne contient
                   pas deja un fichier du même nom */
                $fichiers = $cours->getFichiers();
                foreach ($fichiers as $file) {
                    if($file->getNom() == $nomfichier)
                    {
                        $this->addFlash('fileExist', $nomfichier." existe déjà dans le dossier");

                        // Retourne sur la page de gestion des cours
                        return $this->redirectToRoute('cours_gest');
                    }
                }

                // Emplacement unique
                if ($fichier->guessExtension()) {
                    $extension = $fichier->guessExtension();
                } else {
                    $extension = $fichier->getClientOriginalExtension();
                }

                if(strlen($extension) > 0) $extension = '.'.$extension;

                $emplacement = md5(uniqid()).$extension;

                // Déplacement dans son répertorie
                $fichier->move($this->getParameter('upload_directory'), $emplacement);

                // Màj des données de la bd
                $upload->setEmplacement($emplacement);
                $upload->setNom($nomfichier);
                $upload->setVisible(true);
                $upload->setCours($cours);

                $em->persist($upload);
                $em->flush();

                $upload = new Fichiers();
            }

            return $this->redirectToRoute('cours_gest');
        }
        return $this->render('fichiers/index.html.twig',[
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ent/fichier/{id}/delete", name="fichier_delete")
     */
    public function supprimeFichier(Fichiers $fichier, Request $req)
    {
        // Récupère le prof connecté
        $prof = $this->getUser();

        // Récupère le prof du fichier
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
                // Suppression physique
                $fs = new Filesystem();
                $chemin = $this->getParameter('upload_directory').$fichier->getEmplacement();
                if ($fs->exists($chemin))
                    $fs->remove([$chemin]);

                // Suppression logique BD
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

  /**
  * @Route("/ent/dl/{id}", name="fichier_dl")
  */
  public function download(Fichiers $fichier, Request $req, Filesystem $fs)
  {
    /* Tester si l'utilisateur
    à accès à la ressource */

    // Repertoire des fichiers
    $cheminFichier = $this->getParameter('upload_directory');

    // Nom du fichier
    $nomfichier = $fichier->getEmplacement();

    // Emplacement du fichier sur le serveur
    $cheminFichier = $cheminFichier.$nomfichier;

    // Test si le fichier existe
    if($fs->exists($cheminFichier)){
        /* Si c'est un pdf on essaie de l'ouvrir
           dans le navigateur directement */
        if (strtoupper(pathinfo($nomfichier,PATHINFO_EXTENSION))=="PDF"){
            $resType  = ResponseHeaderBag::DISPOSITION_INLINE;
        } else {
            $resType = ResponseHeaderBag::DISPOSITION_ATTACHMENT;
        }

        // Récupère le nom original
        $nomDL = $fichier->getNom();

        // Renvoie le fichier pour le téléchargement
        return $this->file($cheminFichier, $nomDL, $resType);
    }
    // Sinon le fichier n'existe pas

    // Message d'erreur (a flasher) a templater
    $msg = "Le fichier demandé n'existe plus";

    // Retourne à la page précedente
    $referer = $req->headers->get('referer');
    return $this->redirect($referer);
  }

  /**
  * @Route("/ent/archive/{id}", name="dossier_dl")
  */
  public function makeZip(Cours $cours, Request $req, Filesystem $fs)
  {
    $files = [];
    $em = $this->getDoctrine()->getManager();

    $fichiers = $cours->getFichiers();

    foreach ($fichiers as $fichier) {
        array_push($files, $this->getParameter('upload_directory') . $fichier->getEmplacement());
    }

    // Create new Zip Archive.
    $zip = new \ZipArchive();

    // The name of the Zip documents.
    $zipName = $cours->getNom() . '.zip';

    $zip->open($zipName, \ZipArchive::CREATE);
    foreach ($files as $file) {
        $zip->addFromString(basename($file),  file_get_contents($file));
    }

    if (empty($files))
        $zip->addFromString('vide', '');

    $zip->close();

    // Si le fichier zip n'as pas été généré
    // Redirect to error
    if (!file_exists($zipName))
        echo "<script>window.close();</script>";

    $response = new Response(file_get_contents($zipName));
    $response->headers->set('Content-Type', 'application/zip');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
    $response->headers->set('Content-length', filesize($zipName));

    @unlink($zipName);

    return $response;
  }

    /**
     * @Route("/ent/fichier/{id}/visibilite", name="fichier_visi")
     */
    public function changeVisibilite(Fichiers $fichier, ObjectManager $em)
    {
        if ($fichier->getVisible()) 
            $fichier->setVisible(false);
        else
            $fichier->setVisible(true);

        $em->persist($fichier);
        $em->flush();

        return $this->redirectToRoute('cours_gest');
    }
}

  /* Verifie par sécurité le nom du ficheir transmit */
  // private function verifFileName($filename)
  // {
  //   $filename = pathinfo($filename, PATHINFO_FILENAME);
  //   $filename = @stripslashes(@strip_tags($filename));
  //   if ($filename->guessExtension())
  //   $filename .= '.'.$fichier->guessExtension();
  //   else
  //   $filename .= '.unknown';
  //   return $filename;
  //
  // }

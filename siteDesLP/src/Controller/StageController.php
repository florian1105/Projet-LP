<?php

namespace App\Controller;

use App\Entity\StageForm;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Stage;

class StageController extends AbstractController
{
	/*
	   Status d'un stage :
		formulaire envoyé	ENVOYE
		validé				VALIDE
		signé				SIGNE
		tuteur affecté		COMMENCE
		terminé				TERMINE
	*/
    

    /**
     * Etudiant
     *
     * Affiche le formulaire à remplir pour obtenir une convention de stage.
     * @Route("/stage/nouveau", name="stage_nouveau")
     */
    public function formulaire( Request $request, ObjectManager $manager)
    {

            $stageForm=new StageForm();

            $form = $this->createFormBuilder($stageForm)
                ->add('num_ine')
                ->add('sex')
                ->add('numero_tel_etudiant')
                ->add('mail_perso_etudiant')
                ->add('nom_entreprise')
                ->add('num_siret')
                ->add('addresse_siege_entreprise')
                ->add('code_postal')
                ->add('ville')
                ->add('addresse_stage')
                ->add('nom_prenom_signataire')
                ->add('fonction_signataire')
                ->add('num_tel_signataire')
                ->add('mail_signataire')
                ->add('sujet_stage')
                ->add('nom_tuteur')
                ->add('prenom_tuteur')
                ->add('num_tel_tuteur')
                ->add('mail_tuteur')
                ->add('fonction_tuteur')
                ->add('information_supp')
                ->getForm();

            $form->handleRequest($request);


            return $this->render('stage/index.html.twig', [
                'form' => $form->createView(),
            ]);


    }

	/**
     * Responsable des stages + secretaire + TuteurIUT
     *
     * Afficher la liste des stages selon l'utilisateur.
     * @Route("/stage/rechercher", name="stage_rechercher")
     */
    public function rechercher()
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * Responsable des stages + secretaire + TuteurIUT
     *
     * Affiche un stage avec des options différentes
     * selon l'utilisateur connecté.
     * (valider), (confirmer signatures), (compte rendu / noter)
     * @Route("/stage/{id}", name="stage_afficher")
     */
    public function afficher(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * Responsable des stages
     *
     * ! A voir si faisable dans afficher avec button valider !
     *
     * Valide un stage.
     * @Route("/stage/valider/{id}", name="stage_valider")
     */
    public function valider(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
	 * [Automatique après valider]
	 *
     * Genère une convention au format PDF puis l'envoie :
     *   A l'etudiant (stagiaire)
     *   A l'entrprise (representant + tuteur)
     *   Au chef du departement
     *   Au responsable des stages (enseignant referant)
     */
    public function genererConvention()
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * Secretaire
     *
     * Affiche le formulaire de validation que toutes
     * les signatures ont bien été reçues.
     * @Route("/stage/valider_signatures/{id}", name="stage_valider_signature")
     */
    public function validerSignature(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * Responsable des stages
     *
     * ! A voir si faisable dans afficher avec button valider !
     *
     * Affiche le formulaire d'affectation d'un tuteurIUT au stage.
     * @Route("/stage/affecter_tuteur/{id}", name="stage_affecter")
     */
    public function affecterTuteurIUT(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * TuteurIUT
     *
     * Affiche le formulaire de compte rendu de la visite de stage.
     * @Route("/stage/compte_rendu_visite/{id}", name="stage_compte_rendu")
     */
    public function compteRenduVisite(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * TuteurEntreprise
     *
     * Recu un mail avec lien unique pour faire son evaluation
     * ou accès à la liste de ses stages avec compte ?
     *
     * Affiche le formulaire d'évaluation du stagiaire.
     * @Route("/stage/evaluation/{id}", name="stage_evaluation")
     */
    public function evaluation(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

	/**
     * TuteurIUT
     *
     * ! A voir si faisable dans afficher avec nouveau champs 'note' !
     *
     * Affiche le formulaire de notation d'un stage
     * après délibération avec le jury.
     * @Route("/stage/note/{id}", name="stage_noter")
     */
    public function noter(Stage $stage)
    {
        return $this->render('stage/index.html.twig', [
            'controller_name' => 'StageController',
        ]);
    }

}

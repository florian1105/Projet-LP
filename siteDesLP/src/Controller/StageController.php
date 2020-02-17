<?php

namespace App\Controller;

use App\Entity\ContactEntreprise;
use App\Entity\Entreprises;
use App\Entity\StageForm;
use App\Entity\Ville;
use App\Repository\ContactEntrepriseRepository;
use App\Repository\EntreprisesRepository;
use App\Repository\EtatStageRepository;
use App\Repository\StageFormRepository;
use App\Repository\VilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
    public function formulaire( Request $request, ObjectManager $manager, EtatStageRepository $etatRepo)
    {

        $stageForm=new StageForm();

        $form = $this->createFormBuilder($stageForm)
            ->add('num_ine')
            ->add('sex', ChoiceType::class, [
                'choices'  => [
                    'F' => true,
                    'M' => false,
                ],
            ])
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

        // Réception du form valide -> add/update
        if($form->isSubmitted() && $form->isValid())
        {

            $stageForm->setEtudiant($this->getUser());
            $etatEnvoyer = $etatRepo->findOneBy(["id"=>"1"]);
            $stageForm->setEtat($etatEnvoyer);
            $manager->persist($stageForm);
            $manager->flush();
            $this->addFlash('success','La demande de convention a bien été envoyé');
            return $this->redirectToRoute('stage_informations',[
                'stageForm' => $stageForm
            ]);
        }

        return $this->render('stage/index.html.twig', [
            'form' => $form->createView(),
        ]);


    }

    /**
     * @Route("/stage/informations/{id}",name="stage_informations")
     * @param StageForm $stageForm
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherInformationsStage(StageForm $stageForm=null,StageFormRepository $stageFormRepository){
        if(!$stageForm && $this->getUser()->getRoles()=="ROLE_ETUDIANT"){
            $etudiant=$this->getUser();
            $id=$etudiant->getId();
            $stageForm=$stageFormRepository->findOneBy([
                'etudiant' =>$id,
            ]);
        }
        elseif ($stageForm){
            return $this->render('stage/informations.html.twig',[
                'stageForm' => $stageForm,
            ]);
        }
        return null ;
    }

    /**
     * Responsable des stages + secretaire + TuteurIUT
     *
     * Afficher la liste des stages selon l'utilisateur.
     * @Route("/stage/rechercher", name="stage_rechercher")
     */
    public function rechercher(StageFormRepository $stageFormRepository)
    {
        return $this->render('stage/recherche.html.twig', [
            'stageForms' => $stageFormRepository->findAll()
        ]);
    }

    /**
     * Afficher la liste des stages selon l'utilisateur.
     * @Route("/stage/supprimer/{id}", name="stage_supprimer")
     */
    public function supprimer(StageFormRepository $stageFormRepository)
    {
        return $this->render('stage/recherche.html.twig', [
            'stageForms' => $stageFormRepository->findAll()
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
    public function valider(StageForm $stageForm=null,EtatStageRepository $etatRepo,ObjectManager $manager,VilleRepository $villeRepository, ContactEntrepriseRepository $contactEntRepo,EntreprisesRepository $entRepo)
    {
        if($stageForm){
            $ville = $villeRepository->findOneBy(["nom"=>$stageForm->getVille()]);
            $entreprise = $entRepo->findOneBy(["nom"=>$stageForm->getNomEntreprise()]);
            $tuteur = $contactEntRepo->findOneBy(["mail"=>$stageForm->getMailTuteur()]);
            $signataire = $contactEntRepo->findOneBy(["mail"=>$stageForm->getMailSignataire()]);

            if($ville==null ){
                $ville = new Ville();
                $ville->setNom($stageForm->getVille());
                $ville->setCodePostal($stageForm->getCodePostal());
                $manager->persist($ville);
            }
            if ($entreprise==null){
                $entreprise = new Entreprises();
                $entreprise->setNom($stageForm->getNomEntreprise());
                $entreprise->setVille($ville);
                $entreprise->setNumSiret($stageForm->getNumSIRET());
                $entreprise->setRue($stageForm->getAddresseSiegeEntreprise());
                $entreprise->setValide(true);
            }else{
                $entreprise->setNumSiret($stageForm->getNumSIRET());
            }
            $manager->persist($entreprise);
            if($tuteur==null){
                $tuteur = new ContactEntreprise();
                $tuteur->setNom($stageForm->getNomTuteur());
                $tuteur->setPrenom($stageForm->getPrenomTuteur());
                $tuteur->setMail($stageForm->getMailTuteur());
                $tuteur->setTelephone($stageForm->getNumTelTuteur());
                $tuteur->setFonction($stageForm->getFonctionTuteur());
                $manager->persist($tuteur);
            }
            if($signataire==null){
                $signataire = new ContactEntreprise();
                $signataire->setNom(explode(" ",$stageForm->getNomPrenomSignataire())[0]);
                $signataire->setPrenom(explode(" ",$stageForm->getNomPrenomSignataire())[1]);
                $signataire->setMail($stageForm->getMailSignataire());
                $signataire->setTelephone($stageForm->getNumTelSignataire());
                $signataire->setFonction($stageForm->getFonctionSignataire());
                $manager->persist($signataire);
            }

            $stage = new Stage();
            $stage->setVille($ville);
            $stage->setEntreprise($entreprise);
            $stage->setTuteurEntreprise($tuteur);
            $stage->setSignataire($signataire);
            $stage->setSujet($stageForm->getSujetStage());
            $stage->setCommentaire($stageForm->getInformationSupp());
            $stage->setRue($stageForm->getAddresseStage());
            $etatValider = $etatRepo->findOneBy(["id"=>"2"]);
            $stageForm=$stageForm->setEtat($etatValider);
            $manager->persist($stageForm);
            $manager->persist($stage);

            $manager->flush();

            return $this->redirectToRoute('stage_rechercher');

        }else{
            return $this->render('stage/index.html.twig', [
                'controller_name' => 'StageController',
            ]);
        }
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

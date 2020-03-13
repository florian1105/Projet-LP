<?php

namespace App\Controller;

use App\Entity\ContactEntreprise;
use App\Entity\Entreprises;
use App\Entity\EtatStage;
use App\Entity\Professeurs;
use App\Entity\StageForm;
use App\Entity\Ville;
use App\Repository\ContactEntrepriseRepository;
use App\Repository\EntreprisesRepository;
use App\Repository\EtatStageRepository;
use App\Repository\StageFormRepository;
use App\Repository\VilleRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param EtatStageRepository $etatRepo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formulaire( Request $request, EntityManagerInterface $manager, EtatStageRepository $etatRepo)
    {

        $stageForm=new StageForm();

        $form = $this->createFormBuilder($stageForm)
            ->add('numINE')
            ->add('sex', ChoiceType::class, [
                'choices'  => [
                    'F' => true,
                    'M' => false,
                ],
            ])
            ->add('numeroTelEtudiant')
            ->add('nomEntreprise')
            ->add('numSIRET')
            ->add('addresseSiegeEntreprise')
            ->add('codePostal')
            ->add('ville')
            ->add('addresseStage')
            ->add('nomPrenomSignataire')
            ->add('fonctionSignataire')
            ->add('numTelSignataire')
            ->add('mailSignataire')
            ->add('sujetStage')
            ->add('nomTuteur')
            ->add('prenomTuteur')
            ->add('numTelTuteur')
            ->add('mailTuteur')
            ->add('fonctionTuteur')
            ->add('informationSupp')
            ->getForm();

        $form->handleRequest($request);
        // Réception du form valide -> add/update
        if($form->isSubmitted() && $form->isValid())
        {

            $stageForm->setEtudiant($this->getUser());
            $etatEnvoyer = $etatRepo->findOneBy(["id"=>"1"]);
            $stageForm->setEtatStages($etatEnvoyer);
            $manager->persist($stageForm);
            $manager->flush();

            $this->addFlash('success','La demande de convention a bien été envoyé');
            return $this->redirectToRoute('stage_afficher',[
                'stageForm' => $stageForm,
                'etat' => $etatEnvoyer,
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
        if(!$stageForm && $this->getUser()->getRoles()=="ROLE_ETUDIANT")
        {
            $etudiant=$this->getUser();
            $id=$etudiant->getId();
            $stageForm=$stageFormRepository->findOneBy([
                'etudiant' =>$id,
            ]);
        }
        elseif ($stageForm)
        {
            return $this->render('stage/informations.html.twig',[
                'stageForm' => $stageForm,
                'etat' => $stageForm->getEtatStages()->getNomEtat(),
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
    public function supprimer(StageForm $stageForm ,StageFormRepository $stageFormRepository)
    {
        if($stageForm!=null){
            $em=$this->getDoctrine()->getManager();
            $em->remove($stageForm);
            $em->flush();
            $this->addFlash("success","La demande de convention à bien été supprimer");

        }
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
     * @Route("/stage/afficher", name="stage_afficher")
     */
    public function afficher(StageForm $stageForm = null, StageFormRepository $stageFormRepo)
    {
        if(!$stageForm){
            $stageForm = $stageFormRepo->findOneBy(['etudiant' => $this->getUser()->getId()]);
        }
        return $this->render('stage/informations.html.twig', [
            'stageForm' => $stageForm,
            'etat' => $stageForm->getEtatStages()->getNomEtat(),
        ]);
    }

    /**
     * Responsable des stages
     *
     * ! A voir si faisable dans affichmer avec button valider !
     *
     * Valide un stage.
     * @Route("/stage/valider/{id}", name="stage_valider")
     */
    public function valider(StageForm $stageForm=null,EtatStageRepository $etatRepo,EntityManagerInterface $manager,VilleRepository $villeRepository, ContactEntrepriseRepository $contactEntRepo,EntreprisesRepository $entRepo)
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
                //$entreprise->setRue($stageForm->getAddresseSiegeEntreprise());
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
            $stage->setEtudiant($stageForm->getEtudiant());
            $etatValider = $etatRepo->findOneBy(["id"=>"2"]);
            $stageForm=$stageForm->setEtatStages($etatValider);
            $stageForm->setStage($stage);
            $manager->persist($stageForm);
            $manager->persist($stage);

            $manager->flush();

            return $this->redirectToRoute('stage_generer_convention',[
                'id'=>$stageForm->getId()
            ]);

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
     * @Route("/stage/convention/{id}", name="stage_generer_convention")
     * @param Stage $stage
     * @param Mailer $mailer
     * @param Options $options
     * @return string
     */
    public function genererConvention(StageForm $stageForm = null, \Swift_Mailer $mailer)
    {

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf();


        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setOptions(new Options(['isRemoteEnabled' =>true] ));
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('stage/convention.html.twig', [
            'stageForm' => $stageForm
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);


        // Render the HTML as PDF
        $dompdf->render();


        $pdf = new \Swift_Attachment($dompdf->output(),'convention.pdf');

        $message = (new \Swift_Message())
            ->setSubject('convention de stage')
            ->setFrom('sitedeslp@gmail.com')
            ->setTo([$stageForm->getEtudiant()->getMail()])
            ->setBody($this->renderView('stage/mail_convention.html.twig'))
            ->attach($pdf);

        //envoie du mail
        $mailer->send($message);

        $this->addFlash('success','La convention à bien été générer et envoyer à l\'étudiant');
        return $this->redirectToRoute('stage_rechercher');


    }

    /**
     * Secretaire
     *
     * Affiche le formulaire de validation que toutes
     * les signatures ont bien été reçues.
     * @Route("/stage/afficher/{id}", name="stage_afficher_convention")
     */
    public function afiicherConvention(Stage $stage = null, EntityManagerInterface $manager, EtatStageRepository $etatRepo)
    {


        // Retrieve the HTML generated in our twig file
        return $this->renderView('stage/convention.html.twig', [
            'stage' => $stage
        ]);


    }

    /**
     * Secretaire
     *
     * Affiche le formulaire de validation que toutes
     * les signatures ont bien été reçues.
     * @Route("/stage/valider_signatures/{id}", name="stage_valider_signature")
     */
    public function validerSignature(StageForm $stageForm = null, EntityManagerInterface $manager, EtatStageRepository $etatRepo)
    {


        if($stageForm->getEtatStages()->getNomEtat()=="Valider") {
            $etatEnvoyer = $etatRepo->findOneBy(["id" => "3"]);
            $stageForm->setEtatStages($etatEnvoyer);
            $manager->persist($stageForm);
            $manager->flush();
        }

        return $this->redirectToRoute('stage_informations',[
            'id' => $stageForm->getId(),
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
    public function affecterTuteurIUT(StageForm $stageForm = null, EtatStageRepository $etatRepo,EntityManagerInterface $manager , Request $request)
    {
        $form = $this->createFormBuilder($stageForm)
            ->add('tuteurIUT',EntityType::class, [
                'class' => Professeurs::class,
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $etatCommencer = $etatRepo->findOneBy(["id"=>"4"]);
            $stageForm->setEtatStages($etatCommencer);
            $manager->persist($stageForm);
            $manager->flush();

            $this->addFlash('success','Le Tuteur à bien été enregistrer ');

            return $this->redirectToRoute('stage_rechercher');

        }

        return $this->render('stage/affectation_tuteur.html.twig',[
            'stageForm' => $stageForm,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/stage/gestion",name="stage_gestion")
     */
    public function gestionStage(){
        if($this->getUser()->getStage()!=null){
            return $this->redirectToRoute('stage_afficher');
        }else{
            return $this->redirectToRoute('stage_nouveau');

        }

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

    /**
     * @Route("stage/afficher/attente", name="stage_afficher_attente")
     */
     public function afficherLesStagesEnAttente(StageFormRepository $repoS)
     {
       $lesStagesEnAttente = $repoS->findBy(['etatStages'=>'1']);
       return $this->render('stage/afficherLesStagesEnAttente.html.twig',[
         'lesStagesEnAttente' => $lesStagesEnAttente
       ]);
     }

}

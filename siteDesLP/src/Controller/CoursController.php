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
use App\Repository\ProfesseursRepository;
use App\Repository\ClassesRepository;

class CoursController extends AbstractController
{
    /**
     * @Route("/cours/gerer", name="cours_gerer")
     */
    public function gerer(Request $request) {
        /* Récupère le prof connecté */
        $prof = $this->getUser();

        if(! $prof instanceof Professeurs)
            return $this->redirectToRoute('connexion');

        /* Formulaire d'ajout d'un dossier de cours */
        $cours = new Cours();

        $form = $this->createFormBuilder($cours)
        ->add('nom')
        ->add('classes', EntityType::class,
        [
            'class' => Classes::class,
            'choice_label' => 'nomClasse',
            'label' => 'Classes du dossier',
            'expanded' => true,
            'multiple' => true,
            'mapped' => true,
            'by_reference' => false,
            'query_builder' => function (ClassesRepository $repoC) use ($prof) {
                return $repoC->createQueryBuilder('c')
                    ->andWhere(':id MEMBER OF c.professeurs')
                    ->setParameter('id', $prof->getId())
                    ->orderBy('c.nomClasse', 'ASC');
            }
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
        if($form->isSubmitted() && $form->isValid()) {
            $cours->setProf($prof);
            $cours->setVisible(true);

            $manager = $this->getDoctrine()->getManager();
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
                'query_builder' => function (ClassesRepository $repoC) use ($prof) {
                    return $repoC->createQueryBuilder('c')
                        ->andWhere(':id MEMBER OF c.professeurs')
                        ->setParameter('id', $prof->getId())
                        ->orderBy('c.nomClasse', 'ASC');
                }
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

        /* Récupère les dossiers racines */
        $cours = $prof->getDossiersRacinesCours();

        /* Affichage */
        return $this->render('cours/gestion.html.twig', [
            'dossiersRacines' => $cours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cours/afficher", name="cours_afficher")
     */
    public function afficher(CoursRepository $coursRepo)
    {
        // Récupère l'utilisateur connecté
        $etu = $this->getUser();

        // Vérification que ce soit un etudiant
        if(! $etu instanceof Etudiants)
            return $this->redirectToRoute('connexion');

        // Récupère la classe de l'étudiant
        $classe = $etu->getClasse();

        // Récupère les dossiers de la classe
        $results = $coursRepo->getTreeClasse($classe);

        // Generation de l'affichage de l'arborescence
        // Liste finale d'affichage des dossiers
        $final = [];

        // Vide les enfants des dossiers
// TODO demander au repo de faire ca
        foreach ($results as $dossier) {
            $dossier->clearCoursEnfants();
        }

        foreach ($results as $dossier) {
            if ($dossier->hasParent()) {
                // Recupère l'index du parent du dossier dans la liste 
                $i = getIdCours($dossier, $results);

                // Si le dossier parent est dans la liste
                if ($i >= 0) {
                    // Sous-dossier
                    $dossier->getCoursParent()->addCoursEnfant($dossier);
                } else {
                    // Dossier orphelin
                    $final[] = $dossier;
                }
            } else {
                // Dossier racine
                $final[] = $dossier;
            }
        }

        // Affichage 
        return $this->render('cours/affichage.html.twig', [
            'data' => $final,
        ]);
    }

    /**
     * @Route("/cours/supprimer/{id}", name="cours_supprimer")
     */
    public function supprimer(Cours $cours, Request $req)
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
            return $this->redirectToRoute('cours_gerer');
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
     * @Route("/cours/modifier/{id}", name="cours_modifier")
     */
    public function modifier(Request $request, Cours $cours, ObjectManager $em)
    {
        /* Récupère le prof connecté */
        $prof = $this->getUser();

        if(! $prof instanceof Professeurs)
            return $this->redirectToRoute('connexion');

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
            'query_builder' => function (ClassesRepository $repoC) use ($prof) {
                return $repoC->createQueryBuilder('c')
                    ->andWhere(':id MEMBER OF c.professeurs')
                    ->setParameter('id', $prof->getId())
                    ->orderBy('c.nomClasse', 'ASC');
            }
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
          $em->persist($cours);
          $em->flush();
          $this->addFlash('editTrue','Le dossier a été modifié avec succès');

          return $this->redirectToRoute('cours_gerer');
        }

        return $this->render('cours/edit.html.twig', [
          'form' => $form->createView(),
          'cours' => $cours
        ]);

    }

    /**
     * @Route("/cours/visibilite/{id}", name="cours_visibilite")
     */
    public function changerVisibilite(Cours $cours, ObjectManager $em)
    {
        if ($cours->getVisible()) 
            $cours->setVisible(false);
        else
            $cours->setVisible(true);

        $em->persist($cours);
        $em->flush();

        return $this->redirectToRoute('cours_gerer');
    }
}



/**
 * Fournit une copie du cours sans
 * ses enfants
 * @param $cours Le cours
 * @return La copie
 */
function viderFils($cours) {
    $copie = new Cours();
    $copie->setId($cours->getId());
    $copie->setNom($cours->getNom());
    $copie->setProf($cours->getProf());
    $copie->setVisible($cours->getVisible());
    $copie->copyFichiers($cours->getFichiers());
    return $copie;
}

/**
 * Cherche l'identifiant d'un cours dans une liste de cours.
 * @param $cours Le cours à chercher
 * @param $listeCours La liste dans laquelle chercher
 */
function getIdCours($cours, $listeCours) {
    $i = -1;
    foreach ($listeCours as $index => $dossierFinal) {
        if ($cours->getCoursParent()->getId() == $dossierFinal->getId()){
            $i = $index;
            break;
        }
    }
    return $i;
}
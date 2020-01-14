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
     * @Route("/ent/gestion", name="cours_gest")
     */
    public function gererCours(Request $request) {
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
        print_r('
        <style>
        table, th, td {
          padding: 5px;
          border: 1px solid black;
          border-collapse: collapse;
        }
        </style>
        <table>
        <tr>
        <th>Parent</th>
        <th>ID</th>
        <th>Nom</th>
        <th>Info supplémentaire</th>
        <th>Prof</th>
        <th>Visible</th>
        </tr>');
        foreach ($results as $cours) {
            $id      = $cours->getId();
            $nom     = $cours->getNom();
            $prof    = $cours->getProf();
            $parent  = $cours->getCoursParent();
            $visible = $cours->getVisible();
            
            if($parent != null)
                $parent = $parent->getId();
            else
                $parent = 'null';

            if($prof != null)
                $prof = $prof->getLogin();
            else
                $prof = 'null';
            
        print_r('
        <tr>
        <td style="color:#'.$parent.'7"><strong>'. $parent .'</strong></td>
        <td style="color:#'.$id.'7"><strong>'. $id .'</strong></td>
        <td>'. $nom .'</td>
        <td></td>
        <td>'. $prof .'</td>
        <td>'. $visible .'</td>
        </tr>
        ');
        }
        print_r('</table>');
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
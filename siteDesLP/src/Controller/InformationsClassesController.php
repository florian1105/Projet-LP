<?php

namespace App\Controller;

use App\Entity\InformationsClasses;
use App\Repository\ClassesRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsClassesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class InformationsClassesController extends AbstractController
{

  /**
  * @Route("/information/classe", name="information_classe")
  */
  public function afficherClasse(ClassesRepository $repoC)
  {
    $classes = $repoC->findAll();

    return $this->render('informations_classes/lesclasses.html.twig', [
      'classes' => $classes,
      'nbClasse' => sizeof($classes)
    ]);
  }

  /**
  * @Route("/information/classe/afficher/{id}", name="information_classe_afficher")
  */
  public function afficherInformationClasse(InformationsClasses $info)
  {
    return $this->render('informations_classes/info.html.twig', [
      'info' => $info,
    ]);
  }

  /**
  * @Route("/information/classe/afficher/modifiable", name="information_classe_afficher_modifiable")
  */
  public function afficherInformationClasseModifiable(InformationsClassesRepository $repoI)
  {
    $classe = $this->getUser()->getClasseResponsable();

    $info = $repoI->findBy(['classe' => $classe->getId()]);

    return $this->render('informations_classes/print.html.twig', [
      'info' => $info[0],
      'classe' => $classe
    ]);
  }

  /**
  * @Security("is_granted('ROLE_PROFESSEURRESPONSABLE')")
  * @Route("/information/classe/modifier/{id}", name="informations_classes_modifier")
  */
  public function formulaireInformationClasse(InformationsClasses $info, InformationsClassesRepository $repoI, Request $request, ObjectManager $manager)
  {
    $classe = $this->getUser()->getClasseResponsable();

    $idInfo = $repoI->findBy(['classe' => $info->getClasse()]);

    $cheminPlaquette = $info->getCheminPlaquette();

    //Si le professeur est bien responsable de cette classe
    if($idInfo[0]->getClasse()->getProfesseurResponsable()->getId() == $this->getUser()->getId())
    {
      $cheminPlaquette = $info->getCheminPlaquette();
      $plaquetteDirectory = $this->getParameter('plaquette_directory');
      $plaquette = $plaquetteDirectory . "/" . $cheminPlaquette;
      dump($cheminPlaquette);
      $form = $this->createFormBuilder($info)
      ->add('description', CKEditorType::class, [
        'config' => [
          'uiColor' => '#e2e2e2',
          'toolabar' => 'full',
          'required' => 'true',
          ]])
          ->add('cheminPlaquette', FileType::class , [
            'data_class' => null,
            'mapped' => true,
            'required' => false,
            // 'empty_data' => $plaquette,

          ])

          ->getForm();

          $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid())
          {
              if($form['cheminPlaquette']->getData() == null)
              {
                if($cheminPlaquette == null)
                {
                  $info->setCheminPlaquette(null);
                }
                else
                {
                  //$plaquetteDirectory = $this->getParameter('plaquette_directory');
                  //$plaquette = $plaquetteDirectory . "/" . $cheminPlaquette;
                  $info->setCheminPlaquette($cheminPlaquette);
                }

              }
              else
              {
                $file = $info->getCheminPlaquette();
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                $file->move($this->getParameter('plaquette_directory'),$fileName);
                $info->setCheminPlaquette($fileName);
                $info->setCheminPlaquette($info->getCheminPlaquette());
              }


            $manager->persist($info);
            $manager->flush();

            $this->addFlash('validModificationInformationsClasses',"Les informations ont été modifié avec succès");
            return $this->redirectToRoute('informations_classes_print');
          }


          return $this->render('informations_classes/index.html.twig', [
            'form' => $form->createView(),
            'classe' => $classe
          ]);
        }
        else //Sinon il n'y a pas accès
        {
          $this->addFlash('errorModificationInformationsClasses',"Vous n'êtes pas responsable de cette licence");

          return $this->redirectToRoute('informations_classes_print');

        }
      }

      private function generateUniqueFileName()
      {
        return md5(uniqid());
      }

      //| replace({'/Applications/MAMP/htdocs/Projet-LP/siteDesLP/public/uploads/plaquette':''})
    }

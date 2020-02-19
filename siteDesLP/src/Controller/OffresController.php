<?php

namespace App\Controller;

use DateTime;
use App\Entity\Offres;
use App\Entity\TypeOffre;
use App\Repository\OffresRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffresController extends AbstractController
{

  /**
   * @Security("is_granted('ROLE_CONTACT')")
   * @Route("/offre/rechercher", name="offre_rechercher")
   */
  public function rechercherOffre(OffresRepository $oRepo)
  {
      $entreprise = $this->getUser()->getEntreprise();
      $offres = $oRepo->findBy(["entreprise" => $entreprise]);
      return $this->render('offres/research.html.twig', [
          'offres' => $offres,
      ]);
  }

    /**
     * @Route("/offre/afficher_par_type/{id}", name="offre_afficher_type")
    */
    public function afficherOffres(TypeOffre $typeOffre, OffresRepository $oRepo)
    {
        $offres = $oRepo->findBy(["typeOffre" => $typeOffre]);

        return $this->render('offres/index.html.twig', [
        'offres' => $offres,
        'typeOffre' => $typeOffre
        ]);
    }

/**
  * @Route("/offre/nouveau", name="offre_nouveau")
  * @Route("/offre/modifier/{id}", name="offre_modifier")
  */
  public function formulaireOffre(Offres $offre = null, Request $request, EntityManagerInterface $em)
  {
    $editMode = true;

    if(!$offre)
    {
        $editMode = false;
        $offre = new Offres();
        $offre->setDate(new \DateTime);
        $offre->setEntreprise($this->getUser()->getEntreprise());
    }

      $form = $this->createFormBuilder($offre)
      ->add('titre')
      ->add('description', CKEditorType::class, [
        'config' => [
          'uiColor' => '#e2e2e2',
          'toolbar' => 'full',
          'required' => 'true'
        ]
      ])
      ->add('typeOffre', EntityType::class,
      [
        'class' => TypeOffre::class,
        'choice_label' => 'nomType',

      ])
      ->add('ContratAlternance', ChoiceType::class, [
        'choices'  => [
            '---- Choisir un type de contrat ----' => null,
            'Apprentissage' => 'Apprentissage',
            'Professionnalisation' => 'Professionnalisation'
        ]
    ])
      ->add('dateDuree')
      ->add('remuneration', null, [
        'required' => true
      ])
      ->add('mailContact')
      ->getForm();

      $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($offre);
      $em->flush();
      $this->addFlash('success_modifie','L\'offre a bien été ajouté / mise à jour');
      return $this->redirectToRoute('offre_rechercher');
    }


    return $this->render('offres/formulaire.html.twig', [
      'form_offre' => $form->createView(),
      'offre' => $offre,
      'editMode' => $editMode
    ]);
  }



    /**
     * @Route("/offre/supprimer/{id}", name="offre_supprimer")
    */
    public function supprimerOffre(Offres $offre, Request $request, EntityManagerInterface $em)
    {

        //Si le formulaire à été soumis
        if($request->isMethod('POST'))
        {
        // En cas de validation on supprime et on redirige
        if($request->request->has('oui'))
        {
            $em->remove($offre);
            $em->flush();
            $this->addFlash('delete',"Cet offre a été supprimé avec succès");
            return $this->redirectToRoute('offre_rechercher');
        }

        }
        else
        {
        //Si le formulaire n'a pas été soumis alors on l'affiche
        $title = 'Êtes-vous sûr(e) de vouloir supprimer cet offre ?';

        $message = 'offre n°'.$offre->getId().' ayant pour titre : '.
        $offre->getTitre().' datant du '.
        $offre->getDate()->format('Y-m-d');

        return $this->render('confirmation.html.twig', [
            'titre' => $title,
            'message' => $message
        ]);
        }

    }

  /**
  * @Route("/offre/afficher/{id}", name="offre_afficher")
  */
  public function afficherOffre(Offres $offre)
  {
      return $this->render('offres/show.html.twig', [
      'offre' => $offre
    ]);
  }

}

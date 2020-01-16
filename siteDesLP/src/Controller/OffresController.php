<?php

namespace App\Controller;

use DateTime;
use App\Entity\Offres;
use App\Entity\TypeOffre;
use App\Repository\OffresRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffresController extends AbstractController
{
    /**
     * @Route("/offres/type-offre/{id}", name="offres_id")
    */
    public function afficher(TypeOffre $typeOffre, OffresRepository $oRepo)
    {
        $offres = $oRepo->findBy(["typeOffre" => $typeOffre]);

        return $this->render('offres/index.html.twig', [
        'offres' => $offres
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CONTACT')")
     * @Route("/offres/offres_research", name="offres_research")
     */
    public function researchOffre(OffresRepository $oRepo)
    {
        $entreprise = $this->getUser()->getEntreprise();
        $offres = $oRepo->findBy(["entreprise" => $entreprise]);
        return $this->render('offres/research.html.twig', [
            'offres' => $offres,
        ]);
    }

/**
  * @Route("/offre/add", name="offre_add")
  * @Route("/offre/edit/{id}", name="offre_edit")
  */
  public function form(Offres $offre = null, Request $request, ObjectManager $em)
  {
    $editMode = false;

    if(!$offre)
    {
        $offre = new Offres();

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
        ->getForm();
        $form->handleRequest($request);

        $offre->setDate(new \DateTime);
        $offre->setEntreprise($this->getUser()->getEntreprise());
    }
    else
    { // Mode edit

        $editMode = true;

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
       
        ->getForm();
        $form->handleRequest($request);
    }
      

    if($form->isSubmitted() && $form->isValid())
    {
      $em->persist($offre);
      $em->flush();
      $this->addFlash('success_modifie','L\'offre a bien été ajouté / mise à jour');
      return $this->redirectToRoute('offres_research');
    }


    return $this->render('offres/formulaire.html.twig', [
      'form_offre' => $form->createView(),
      'offres' => $offre,
      'editMode' => $editMode
    ]);
  }



    /**
     * @Route("/offres/remove/{id}", name="offres_delete")
    */
    public function delete(Offres $offre, Request $request, ObjectManager $em)
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
            return $this->redirectToRoute('offres_research');
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
    
}

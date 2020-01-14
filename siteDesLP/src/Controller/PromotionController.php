<?php

namespace App\Controller;

use App\Entity\Promotions;
use App\Repository\PromotionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;


class PromotionController extends AbstractController
{
  /**
  * @Route("/promotion/new", name="promotion_create")
  * @Route("/promotion/edit/{id}", name="promotion_edit")
  */
  public function form(Promotions $promotion = null,Request $request, ObjectManager $manager)
  {
    $editMode = true;
    if(!$promotion)
    {
      $promotion = new Promotions();
      $editMode = false;
      $form = $this->createFormBuilder($promotion)
      ->add('annee')
      ->getForm();
      $form->handleRequest($request);
    }

    else
    {
      $form = $this->createFormBuilder($promotion)
      ->add('annee')
      ->getForm();
      $form->handleRequest($request);
    }



    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($promotion);
      $manager->flush();
      if($editMode = false)
      {
        $this->addFlash('successAdd','La promotion a bien été crée');
      }
      else
      {
        $this->addFlash('successModif','La promotion a bien été modifié');
      }

      return $this->redirectToRoute('promotion_research');

    }

    return $this->render('promotion/index.html.twig', [
      'form_create_promotion' => $form->createView(),
      'editMode' => $editMode,

    ]);

  }

  /**
  * @Route("/promotion/remove/{id}", name="promotion_delete")
  */
  public function delete(Promotions $promotion, Request $req, ObjectManager $manager)
  {
    if($req->isMethod('POST'))
    {
      // En cas de validation on supprime et on redirige
      if($req->request->has('oui'))
      {


        $manager->remove($promotion);
        $manager->flush();
        $this->addFlash('delete',"La promotion a été supprimé avec succès");
      }
      return $this->redirectToRoute('promotion_research');
    }

    else
    {
      //Si le formulaire n'a pas été soumis alors on l'affiche
      $title = 'Êtes-vous sûr(e) de vouloir supprimer cette promotion ?';

      $message = 'N°'.$promotion->getId().' année : '. $promotion->getAnnee();


      return $this->render('confirmation.html.twig', [
        'titre' => $title,
        'message' => $message
      ]);
    }
  }

  /**
   * @Route("promotion/promotion_research", name="promotion_research")
   */
public function researchPromotion(PromotionsRepository $repoP)
{
  $promotions = $repoP->findAll();
  return $this->render('promotion/research.html.twig', [
    'promotions' => $repoP->findAll(),
  ]);

}

}

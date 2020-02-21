<?php

namespace App\Controller;

use App\Entity\InformationsGlobales;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsGlobalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class InformationsGlobalesController extends AbstractController
{
    /**
     * @Route("/information/globale/afficher", name="information_globale_afficher")
     */
    public function afficherInformationGlobale(InformationsGlobalesRepository $repoI)
    {
            $info = $repoI->find(1);
            
        return $this->render('informations_globales/info.html.twig', [
            'info' => $info,
        ]);
    }

    /**
     * @Route("/information/globale/modifier/{id}", name="information_globale_modifier")
     */
    public function formulaireInformationGlobale(InformationsGlobales $info, Request $request, EntityManagerInterface $manager)
    {

			$form = $this->createFormBuilder($info)
            ->add('description', CKEditorType::class, [
                'config' => [
                  'uiColor' => '#e2e2e2',
                  'toolabar' => 'full',
                  'required' => 'true'
                ]])
		 	->getForm();

	        $form->handleRequest($request);


		if($form->isSubmitted() && $form->isValid())
		{
			$manager->persist($info);
			$manager->flush();

			$this->addFlash('validModificationInformationsGlobales',"Les informations ont été modifié avec succès");
		}


        return $this->render('informations_globales/index.html.twig', [
			'form' => $form->createView(),
        ]);
    }
}

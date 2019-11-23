<?php

namespace App\Controller;

use App\Entity\InformationsGlobales;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsGlobalesRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InformationsGlobalesController extends AbstractController
{
    /**
     * @Route("/informations/globales", name="informations_globales")
     */
    public function print(InformationsGlobalesRepository $repoI)
    {
            $info = $repoI->find(1);
            
        return $this->render('informations_globales/info.html.twig', [
            'info' => $info,
        ]);
    }

    /**
     * @Route("/informations/globales/edit/{id}", name="informations_globales_edit")
     */
    public function form(InformationsGlobales $info, Request $request, ObjectManager $manager)
    {

			$form = $this->createFormBuilder($info)
				->add('description', TextareaType::class, [
                    'help_html' => true,
                    'attr' => ['rows' => 23]
                ])
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

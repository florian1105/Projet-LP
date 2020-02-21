<?php

namespace App\Controller;

use App\Entity\Date;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DateController extends AbstractController
{
    /**
     * @Route("/date/modifier/{id}", name="date_modifier")
     */
    public function formulaire(Date $date, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder($date)
            ->add('date', DateType::class, [
                'widget' => 'single_text'
            ])
            ->getForm();

        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($date);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('date/index.html.twig', [
            'form_create_date' => $form->createView(),
            'date' => $date
        ]);
    }
}

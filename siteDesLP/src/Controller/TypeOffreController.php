<?php

namespace App\Controller;

use App\Repository\TypeOffreRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TypeOffreController extends AbstractController
{
    /**
     * @Route("/type/offre", name="type_offre")
     */
    public function index(TypeOffreRepository $tRepo)
    {
        $types = $tRepo->findAll();

        return $this->render('type_offre/index.html.twig', [
            'types' => $types
        ]);
    }
}

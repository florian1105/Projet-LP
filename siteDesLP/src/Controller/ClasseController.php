<?php

namespace App\Controller;

use App\Entity\Classes;
//use Symfony\Component\HttpFoundation\Response;
use App\Entity\Professeurs;

use App\Repository\ClassesRepository;
use App\Repository\ProfesseursRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilder;

class ClasseController extends AbstractController
{
    /**
     * @Route("/classe/new", name="classe_create")
     * @Route("/classe/{id}/edit", name="classe_edit")
     */
  public function form(Classes $classe = null, ClassesRepository $repoC, Request $request, ObjectManager $manager)
  {
    if(!$classe)
    {
      $classe = new Classes();
    }

    $form = $this->createFormBuilder($classe)
    ->add('nomClasse')
    ->add('professeurResponsable',
	  EntityType::class,
	  [
    'class' => Professeurs::class,
    'required' => true,
		'query_builder' => function (ProfesseursRepository $repoP) use ($repoC) {
    $g = $repoC->createQueryBuilder('c')
			->select('IDENTITY(c.professeurResponsable)')
			->getQuery()
      ->getArrayResult();
      
    $intcast = [];
    foreach ($g as $key => $value) 
    {
        foreach ($value as $key2 => $value2) 
        {
          if($value2 === null)  $intcast[] = null;
          else $intcast[] = intval($value2);
        }
      }

    $query = $repoP->createQueryBuilder('p')
    ->where($repoP->createQueryBuilder('p')->expr()->notIn('p.id', $intcast));

    return $query; }])

      ->getForm();

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
      $manager->persist($classe);
      $manager->flush();

      return $this->redirectToRoute('classe_research');
    }

    return $this->render('classe/index.html.twig', [
      'form_create_classe' => $form->createView(),
      'editMode' => $classe->getId() !== null,
      'classe' => $classe
    ]);
  }

    /**
     * @Route("classe/classe_delete/{id}", name="classe_delete")
     */
    public function deleteClasse(Classes $classe, Request $req)
    {

    //Si le formulaire à été soumis
    if($req->isMethod('POST')){
        // En cas de validation on supprime et on redirige
      if($req->request->has('oui')) {
        $em=$this->getDoctrine()->getManager();
        $em->remove($classe);
        $em->flush();
      }
      // Sinon on redirige simplement
      return $this->redirectToRoute('classe_research');
    } else {
      //Si le formulaire n'a pas été soumis alors on l'affiche
      $title = 'Êtes-vous sûr(e) de vouloir supprimer ce professeur ?';

      $message = 'N°'.$classe->getId().' : '.
        $classe->getNomClasse();

        return $this->render('confirmation.html.twig', [
          'titre' => $title,
          'message' => $message
            ]);
        }
    }

    /**
     * @Route("classe/classe_research", name="classe_research")
     */
    public function researchClasse(ClassesRepository $repoC)
    {
        $classes =$repoC->findAll();
        return $this->render('classe/research.html.twig', [
            'classes' => $classes,
        ]);
    }

}

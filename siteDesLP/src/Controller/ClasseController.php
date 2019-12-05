<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Entity\Professeurs;
use App\Entity\InformationsClasses;
use App\Repository\ClassesRepository;
use Symfony\Component\Form\FormBuilder;
use App\Repository\ProfesseursRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClasseController extends AbstractController
{
    /**
     * @Route("/classe/new", name="classe_create")
     * @Route("/classe/{id}/edit", name="classe_edit")
     */
  public function form(Classes $classe = null, ClassesRepository $repoC, Request $request, ObjectManager $manager)
  {
    $editMode = true;

    if(!$classe)
    {
      $classe = new Classes();
      $editMode = false;
    }
    if(!$editMode)
    {
      $prof = $classe->getProfesseurResponsable();
      $form = $this->createFormBuilder($classe)
      ->add('nomClasse')
      ->add('nomComplet')
      ->add('professeurResponsable',
      EntityType::class,
      [
      'class' => Professeurs::class,
      'required' => true,
      'query_builder' => function (ProfesseursRepository $repoP) use ($repoC) {
      $profResps = $repoC->createQueryBuilder('c')
        ->select('IDENTITY(c.professeurResponsable)');
//        ->getQuery()
//        ->getArrayResult();

//      $idprofs = [];
//      foreach ($profResps as $key => $value)
//      {
//          foreach ($value as $key2 => $value2)
//          {
//            if($value2 === null)  $idprofs[] = null;
//            else $idprofs[] = intval($value2);
//          }
//      }
      $query = $repoP->createQueryBuilder('p')
      ->where($repoP->createQueryBuilder('p')->expr()->notIn('p.id', $profResps->getDQL()));

      return $query; }])

        ->getForm();

      $form->handleRequest($request);
      $nomClasse = "LP - ".strtoupper($form['nomClasse']->getData());

      if($repoC->findBy(['nomClasse' => $nomClasse]) == null)
      { 
        if($form->isSubmitted() && $form->isValid())
        {
          $classe->setNomClasse($nomClasse);
          $info = new InformationsClasses();
          $info->setClasse($classe);
          $info->setDescription("Non définies");
          $manager->persist($classe);
          $manager->persist($info);
          $manager->flush();
          $this->addFlash('success','La classe a bien été créée');
          return $this->redirectToRoute('classe_research');
        }
      }
      else $this->addFlash('errorAjouterClasse',"Ce nom de classe existe déjà");
    }
    else
    {
      $prof = $classe->getProfesseurResponsable();
      $form = $this->createFormBuilder($classe)
      ->add('nomClasse')
      ->add('nomComplet')
      ->add('professeurResponsable',
      EntityType::class,
      [
      'class' => Professeurs::class,
      'required' => true,
      'query_builder' => function (ProfesseursRepository $repoP) use ($repoC, $prof) {
      $profResps = $repoC->createQueryBuilder('c')
        ->select('IDENTITY(c.professeurResponsable)')
        ->getQuery()
        ->getArrayResult();

      $idprofs = [];
      foreach ($profResps as $key => $value)
      {
          foreach ($value as $key2 => $value2)
          {
            if($value2 === null)  $idprofs[] = null;
            else $idprofs[] = intval($value2);
          }
      }

      $query = $repoP->createQueryBuilder('p')
      ->where($repoP->createQueryBuilder('p')->expr()->notIn('p.id', $idprofs))
      ->orWhere('p.id = :id')
      ->setParameter('id', $prof->getId());

      return $query; }])

        ->getForm();

      $nomClasse0 = explode( 'LP - ',$form['nomClasse']->getData()); //On enlève le "LP - " devant le nom de classe

      $form['nomClasse']->setData($nomClasse0[1]); //On set l'input avec le nom classe sans le "LP - "

      $nomClasse = "LP - ".strtoupper($form['nomClasse']->getData()); //On rajoute le "LP - " pour le if suivant

      $form->handleRequest($request);

      if($repoC->findBy(['nomClasse' => "LP - ".$classe->getNomClasse()]) == null || $nomClasse == "LP - ".strtoupper($form['nomClasse']->getData())) //Si ce nom n'existe pas encore ou qu'il est l'actuel 
      { 
        if($form->isSubmitted() && $form->isValid())
        {
          $classe->setNomClasse("LP - ".strtoupper($form['nomClasse']->getData()));
          $manager->persist($classe);
          $manager->flush();
          $this->addFlash('success_modifie','les changements on biens été pris en compte');
          return $this->redirectToRoute('classe_research');
        }
      }
      else $this->addFlash('errorAjouterClasse',"Ce nom de classe existe déjà");

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
    if($req->isMethod('POST'))
    {
        // En cas de validation on supprime et on redirige
      if($req->request->has('oui'))
      {
        if(sizeof($classe->getEtudiants()) != 0)
				{
					$this->addFlash('errorSuppressionClasse',"Cette classe ne peut pas être supprimé car elle contient des étudiants");
				}
				else
				{
          $em=$this->getDoctrine()->getManager();
          $em->remove($classe);
          $em->flush();
          $this->addFlash('delete',"La classe a été supprimé avec succès");
        }
      }
      // Sinon on redirige simplement
      return $this->redirectToRoute('classe_research');
    }
    else
    {
      //Si le formulaire n'a pas été soumis alors on l'affiche
      $title = 'Êtes-vous sûr(e) de vouloir supprimer cette classe ?';

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

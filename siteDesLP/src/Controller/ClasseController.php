<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Entity\Etudiants;
use App\Entity\Promotions;
use App\Entity\Professeurs;
use App\Repository\DateRepository;
use App\Entity\InformationsClasses;
use App\Repository\ClassesRepository;
use App\Repository\EtudiantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilder;
use App\Repository\PromotionsRepository;
use App\Repository\ProfesseursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClasseController extends AbstractController
{
  /**
  * @Route("/classe/nouveau", name="classe_nouveau")
  * @Route("/classe/modifier/{id}", name="classe_modifier")
  */
  public function formulaireClasse(Classes $classe = null, ProfesseursRepository $repoProf, PromotionsRepository $repoP, ClassesRepository $repoC, Request $request, EntityManagerInterface $manager)
  {
    $editMode = true;
    if(!$classe)
    {
      if(sizeof($repoProf->findAll()) <= sizeof($repoC->findAll()))
      {
        $this->addFlash('erreurProfDisponible','Pas assez de professeur disponible pour créer une nouvelle classe');
        return $this->redirectToRoute('classe_rechercher');
      }
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
          $query = $repoP->createQueryBuilder('p')
          ->where($repoP->createQueryBuilder('p')->expr()->notIn('p.id', $profResps->getDQL()));

          return $query; }
        ])
        ->getForm();

        $form->handleRequest($request);
        $nomClasse = "LP - ".strtoupper($form['nomClasse']->getData());

        if($repoC->findBy(['nomClasse' => $nomClasse]) == null)
        {
          if($form->isSubmitted() && $form->isValid())
          {
            $annee = date('Y');
            $mois = date('n');


            $dateActuelle = Promotions::getPromo($annee, $mois);

            $unePromo = $repoP->findOneBy(['annee' => $dateActuelle]);
            if($unePromo == null)
            {
              $currentPromo = new Promotions();
              $currentPromo->setPromo($annee, $mois);
              $classe->addPromotion($currentPromo);
              $manager->persist($currentPromo);
            }
            else
            {
              $classe->addPromotion($unePromo);
            }





            $classe->setNomClasse($nomClasse);
            $info = new InformationsClasses();
            $info->setClasse($classe);
            $info->setDescription("Non définies");
            $manager->persist($classe);
            $manager->persist($info);
            $manager->flush();
            $this->addFlash('success','La classe a bien été créée');
            return $this->redirectToRoute('classe_rechercher');
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
                $this->addFlash('success_modifie','Cette classe a bien été modifié');
                return $this->redirectToRoute('classe_rechercher');
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
        * @Route("classe/supprimer/{id}", name="classe_supprimer")
        */
        public function supprimerClasse(Classes $classe, Request $req)
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
            return $this->redirectToRoute('classe_rechercher');
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
        * @Route("classe/rechercher", name="classe_rechercher")
        */
        public function rechercherClasse(ClassesRepository $repoC)
        {
          $classes =$repoC->findAll();
          return $this->render('classe/research.html.twig', [
            'classes' => $classes,
          ]);
        }

        /**
        * @Route("classe/purger/{id}", name="classe_purger")
        */
        public function purgerClasse(Classes $classe, EtudiantsRepository $repoE, EntityManagerInterface $em, PromotionsRepository $repoPr, Request $req, DateRepository $repoD)
        {
          if($req->isMethod('POST'))
          {

            if($req->request->has('oui'))
            {
              $lesEtudiants = $repoE->findBy(['classe' => $this->getUser()->getClasseResponsable()]);
              foreach ($lesEtudiants as $unEtudiant)
              {
                $unEtudiant->setMailAcademique(null);
                $em->persist($unEtudiant);
              }

              $promoSuivante = strval(date('Y') + 1) . '/' . strval(date('Y') + 2);

              $promo = $repoPr->findOneBy(['annee' => $promoSuivante]);

              if($promo == null)
              {
                $anneeDebut = strval(date('Y') + 1);
                $anneeFin = strval(date('Y') + 2);
                $promo = new Promotions();
                $promo->setAnneeDebut($anneeDebut);
                $promo->setAnneeFin($anneeFin);
                $promo->setAnnee($anneeDebut, $anneeFin);

                $classe->addPromotion($promo);
                $em->persist($promo);
                $em->persist($classe);



              }
              else
              {
                $classe->addPromotion($promo);
              }

              $repoD->find(1)->setClique(true);

              $em->flush();
              $this->addFlash('purge','La classe a bien été purgé');
              return $this->redirectToRoute('etudiant_rechercher');
            }
          }
            else
            {
              //Si le formulaire n'a pas été soumis alors on l'affiche
              $title = 'Êtes-vous sûr(e) de vouloir purger cette classe, cela aura pour effet de supprimer les mails académiques des étudiants et de passer à la prochaine promotion';


              return $this->render('confirmation.html.twig', [
                'titre' => $title,
                'message' => ''
              ]);
            }





        }
      }

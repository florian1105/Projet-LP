<?php

namespace App\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use App\Entity\Classes;
use App\Entity\Contacts;
use App\Services\Mailer;
use App\Entity\Etudiants;
use League\Csv\Exception;
use App\Entity\Entreprises;
use App\Repository\DateRepository;


use App\Form\ResettingPasswordType;
use App\Repository\EtudiantsRepository;
use App\Repository\SecretaireRepository;
use App\Repository\ProfesseursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class EtudiantController extends AbstractController
{

	public function str_to_noaccent($str)
	{
		$url = $str;
		$url = preg_replace('#Ç#', 'C', $url);
		$url = preg_replace('#ç#', 'c', $url);
		$url = preg_replace('#è|é|ê|ë#', 'e', $url);
		$url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
		$url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
		$url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
		$url = preg_replace('#ì|í|î|ï#', 'i', $url);
		$url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
		$url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
		$url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
		$url = preg_replace('#ù|ú|û|ü#', 'u', $url);
		$url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
		$url = preg_replace('#ý|ÿ#', 'y', $url);
		$url = preg_replace('#Ý#', 'Y', $url);

		return ($url);
	}


	/**
	* @Route("/etudiant/nouveau", name="etudiant_nouveau")
	* @Route("/etudiant/modifier/{id}", name="etudiant_modifier")
	*/
	public function formulaireEtudiant(Etudiants $etudiant = null, Etudiantsrepository $repoE, ProfesseursRepository $repoP, SecretaireRepository $repoS, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
	{
		$classe = "";
		$editMode = true;

		if(!$etudiant)
		{
			$etudiant = new Etudiants();
			$editMode = false;
		}
		if($this->getUser()->getRoles()[0] == "ROLE_PROFESSEURRESPONSABLE") //Si l'utilisateur est un professeur responsable
		{
			if($editMode == false)
			{
				$form = $this->createFormBuilder($etudiant)
				->add('nom')
				->add('prenom')
				->add('new_password', PasswordType::class, [
					'attr' => ['maxlength' => '64']
				])
				->add('confirm_password', PasswordType::class, [
					'attr' => ['maxlength' => '64'],
				])
				->add('mail')
				->add('date_naissance', DateType::class, [
					'widget' => 'single_text'
				])
				->getForm();

				$form->handleRequest($request);


				$prenomLogin = strtolower($this->str_to_noaccent($form['prenom']->getData()));
				$prenomLogin1 = substr($prenomLogin, 0,1);
				$login = strtolower($form['nom']->getData()).$prenomLogin1;
				$mailAcademique = $prenomLogin.".".strtolower($form['nom']->getData());

				$i = "";
				$j = "";

				while($repoE->findBy(['login' => $login.$i]) || $repoP->findBy(['login' => $login.$i]) || $repoS->findBy(['login' => $login.$i]) )
				{
					if($i == "") $i = 0;
					$i++;
				}

				while($repoE->findBy(['mailAcademique' => $mailAcademique.$j."@etu.umontpellier.fr"]))
				{
					if($j == "") $j = 0;
					$j++;
				}

				$etudiant->setLogin($login.$i);
				$etudiant->setMailAcademique($mailAcademique.$j."@etu.umontpellier.fr");
				$classe = $this->getUser()->getClasseResponsable();
				$promoEtudiant = $etudiant->getDernierePromo($classe);
				$etudiant->setPromotion($promoEtudiant);

			}
			else
			{

				$form = $this->createFormBuilder($etudiant)
				->add('nom')
				->add('prenom')
				->add('login')
				->add('mail')
				->add('mailAcademique')
				->add('date_naissance', DateType::class, [
					'widget' => 'single_text'
				])

				->getForm();

				$form->handleRequest($request);

				$mailAca = strtolower($form['mailAcademique']->getData());
				$etudiant->setMailAcademique($mailAca);
			}

			$mail = strtolower($form['mail']->getData());
			$prenom = ucfirst(strtolower($form['prenom']->getData()));
			$nom = strtoupper($form['nom']->getData());

			$etudiant->setMail($mail);
			$etudiant->setnom($nom);
			$etudiant->setPrenom($prenom);

			if($form->isSubmitted() && $form->isValid())
			{
				if($editMode == false)
				{
					$hash = $encoder->encodePassword($etudiant, $etudiant->getNewPassword());
					$etudiant->setPassword($hash);
				}
				$etudiant->setClasse($this->getUser()->getClasseResponsable());
				$em->persist($etudiant);
				$em->flush();


				return $this->redirectToRoute('etudiant_rechercher');

			}
		}
		else if($editMode == false) //Sinon si l'utilisateur n'est pas prof responsable
		{
			$form = $this->createFormBuilder($etudiant)
			->add('nom')
			->add('prenom')
			->add('new_password', PasswordType::class, [
				'attr' => ['maxlength' => '64']
			])
			->add('confirm_password', PasswordType::class, [
				'attr' => ['maxlength' => '64'],
			])
			->add('mail')
			->add('date_naissance', DateType::class, [
				'widget' => 'single_text'
			])
			->add('classe', EntityType::class, [
				'class' => Classes::class,
				'choice_label' => 'nomClasse',
		])

			->getForm();
			$form->handleRequest($request);

			$prenomLogin = strtolower($this->str_to_noaccent($form['prenom']->getData()));
			$prenomLogin1 = substr($prenomLogin, 0,1);
			$login = strtolower($form['nom']->getData()).$prenomLogin1;
			$mailAcademique = $prenomLogin.".".strtolower($form['nom']->getData());

			$i = "";
			$j = "";

			while($repoE->findBy(['login' => $login.$i]) || $repoP->findBy(['login' => $login.$i]) || $repoS->findBy(['login' => $login.$i]))
			{
				if($i == "") $i = 0;
				$i++;
			}

			while($repoE->findBy(['mailAcademique' => $mailAcademique.$j."@etu.umontpellier.fr"]))
			{
				if($j == "") $j = 0;
				$j++;
			}

			$etudiant->setLogin($login.$i);
			$etudiant->setMailAcademique($mailAcademique.$j."@etu.umontpellier.fr");

		}
		else
		{
			$form = $this->createFormBuilder($etudiant)
			->add('nom')
			->add('prenom')
			->add('login')
			->add('mail')
			->add('mailAcademique')
			->add('date_naissance', DateType::class, [
				'widget' => 'single_text'
			])
			->add('classe', EntityType::class, [
				'class' => Classes::class,
				'choice_label' => 'nomClasse',
			])

			->getForm();
			$form->handleRequest($request);
			$mailAca = strtolower($form['mailAcademique']->getData());
			$etudiant->setMailAcademique($mailAca);


		}

		$mail = strtolower($form['mail']->getData());
		$prenom = ucfirst(strtolower($form['prenom']->getData()));
		$nom = strtoupper($form['nom']->getData());

		$etudiant->setMail($mail);
		$etudiant->setnom($nom);
		$etudiant->setPrenom($prenom);




		if($form->isSubmitted() && $form->isValid())
		{
			if($editMode == false)
			{
				$classeEtudiant = $etudiant->getClasse();
				$promoEtudiant = $etudiant->getDernierePromo($classeEtudiant);
				$etudiant->setPromotion($promoEtudiant);
				$hash = $encoder->encodePassword($etudiant, $etudiant->getNewPassword());
				$etudiant->setPassword($hash);
				$this->addFlash('success','L\'étudiant a bien été créé');
			}
			else
			{
				$this->addFlash('success_modifie','Les changements on biens été pris en compte');
			}
			$em->persist($etudiant);
			$em->flush();


			return $this->redirectToRoute('etudiant_rechercher');

		}

		return $this->render('etudiant/index.html.twig', [
			'form_create_etudiant' => $form->createView(),
			'editMode' => $etudiant->getId() !== null,
			'etudiant' => $etudiant,
			'classe' => $classe
		]);
	}

	/**
	 * @Route("etudiant/supprimer/{id}", name="etudiant_supprimer")
	 */
	public function supprimerEtudiant(Etudiants $etudiant, Request $req)
	{
		//Si le formulaire à été soumis
		if($req->isMethod('POST'))
		{
		// En cas de validation on supprime et on redirige
			if($req->request->has('oui')) 
			{
				$em=$this->getDoctrine()->getManager();
				$em->remove($etudiant);
				$em->flush();
				$this->addFlash('delete','Étudiant supprimé');
			}
			// Sinon on redirige simplement
			return $this->redirectToRoute('etudiant_rechercher');
		}
		else
		{
			//Si le formulaire n'a pas été soumis alors on l'affiche
			$title = 'Êtes-vous sûr(e) de vouloir supprimer cet étudiant ?';

			$message = 'N°'.$etudiant->getId().' : '.
			$etudiant->getPrenom().' '.
			$etudiant->getnom(). ' ('.
			$etudiant->getLogin().')';


			return $this->render('confirmation.html.twig', [
			'titre' => $title,
			'message' => $message
			]);
		}

	}

	/**
	 * @Route("etudiant/rechercher", name="etudiant_rechercher")
	 */
	public function rechercherEtudiant(EtudiantsRepository $repoE)
	{
		if($this->getUser()->getRoles()[0] == "ROLE_PROFESSEURRESPONSABLE") //Si l'utilisateur est un professeur responsable
		{
			$classe = $this->getUser()->getClasseResponsable(); //Récupère la classe courante du professeur responsable

			$etudiants = $repoE->getEtudiantsByClasse($classe); //Récupère les étudiants de la classe du professeur

			return $this->render('etudiant/research.html.twig', [
					'etudiants' => $etudiants,
					'classeID' => $classe->getId(),
					'mois' => date('n')
			]);
		}
		else
		{
			$etudiants = $repoE->findAll(); //Récupère tous les étudiants

			for ($i= 0 ; $i < sizeof($etudiants); $i++) //Pour chaque étudiant
        	{ 
            	if($etudiants[$i]->getMailAcademique() == null) unset($etudiants[$i]); //Si il n'a pas de mail académique on le supprime
			}
		
		}
			return $this->render('etudiant/research.html.twig', [
					'etudiants' => $etudiants,

			]);
	}

	/**
	 * @Route("ancien_etudiant/rechercher", name="ancien_etudiant_rechercher")
	 */
	public function rechercherAncienEtudiant(EtudiantsRepository $repoE)
	{
		$etudiants = $repoE->findBy(['mailAcademique' => null]);

		return $this->render('etudiant/researchAncien.html.twig', [
				'etudiants' => $etudiants,
		]);
	}

	/**
	 * @Route("etudiant/transformer_contact/{id}", name="etudiant_transformer_contact")
	 */
	public function transformerEtudiantEnContact(Etudiants $etudiant, Request $request, EntityManagerInterface $manager)
	{
		if (!$etudiant->isAncienEtudiant()) {
			$this->addFlash('error', 'Cette étudiant n\' a pas été détecté comme étant un ancien étudiant. Procédure annulée.');
			return $this->redirectToRoute('anciens_etudiants_recherche');
		}

		$contact = new Contacts();
		$contact->setNom($etudiant->getNom())
		->setPrenom($etudiant->getPrenom())
		->setMail($etudiant->getMail())
		//->setTelephone()
		//->setEntreprise()
		->setValide(true)
		->setPassword($etudiant->getPassword())
		//->setNewPassword($etudiant->getNewPassword())
		//->setPasswordRequestedAt($etudiant->getPasswordRequestedAt())
		//->setConfirmPassword($etudiant->getConfirmPassword())
		//->setToken($etudiant->getToken())
		;


		$form = $this->createFormBuilder($contact)
		->add('nom')
		->add('prenom')
		->add('mail')
		->add('telephone')
		->add('entreprise', EntityType::class, [
		  'class' => Entreprises::class,
		  'choice_label' => 'Nom',
		])
		->getForm();

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$this->addFlash('success_modifie', 'Le contact a bien été modifié');

			$manager->persist($contact);
			$manager->remove($etudiant);
			$manager->flush();

			return $this->redirectToRoute("contact_search_valide");

			//return $this->redirectToRoute("contact_valide",['id'=>$contact->getId()]);
		}

		return $this->render('contacts/index.html.twig', [
			'form' => $form->createView(),
			'editMode' => $contact->getId() !== null,
			'contacts' => $contact
		]);
	}

	/**
	 * @Route("etudiant/compte", name="etudiant_compte")
	 */
	public function compteEtudiant(UserInterface $etudiant)
	{
			$etudiant = $this->getUser();
			return $this->render('etudiant/moncompte.html.twig', [
					'etudiant' => $etudiant,
			]);
	}

	/**
	 * @Route("etudiant/compte/changer_mdp", name="etudiant_changer_mdp")
	 */
	public function changerMdp(UserInterface $etudiant, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
	{
			$etudiant = $this->getUser();

			$form = $this->createFormBuilder($etudiant)
			->add('password', PasswordType::class, array('mapped' => false))
			->add('new_password', PasswordType::class)
			->add('confirm_password', PasswordType::class)


			->getForm();


			$form->handleRequest($request);

			$mdpNonChange = "";

			if($form->isSubmitted() && $form->isValid())
			{
				$match = $encoder->isPasswordValid($etudiant, $form['password']->getData());
				//si password valide
				if($match)
				{
					$hash = $encoder->encodePassword($etudiant, $form['new_password']->getData());
					$etudiant->setPassword($hash);
					$em->persist($etudiant);
					$em->flush();
					$this->addFlash('mdp_change','Votre mot de passe a été modifié avec succès');
					return $this->redirectToRoute('etudiant_compte');
				}
				else {
					$mdpNonChange = "Le mot de passe entré n'est pas votre mot de passe actuel";
				}
			}



			return $this->render('etudiant/changepassword.html.twig', [
					'etudiant' => $etudiant,
					'form_change_password' => $form->createView(),
					'error' => $mdpNonChange,
			]);
	}



		/**
		 * @Route("etudiant/compte/changer_mail", name="etudiant_changer_mail")
		 */
		public function changerMail(UserInterface $etudiant, Request $request, EntityManagerInterface $em)
		{
				$etudiant = $this->getUser();

				$form = $this->createFormBuilder($etudiant)
			->add('mail')


			->getForm();

			$form->handleRequest($request);


			if($form->isSubmitted() && $form->isValid())
			{
					$em->persist($etudiant);
					$em->flush();
					$this->addFlash('mail_change','Votre mail a été modifié avec succès');
					return $this->redirectToRoute('etudiant_compte');

			}

			return $this->render('etudiant/changeemail.html.twig', [
					'etudiant' => $etudiant,
					'form_change_email' => $form->createView()
			]);
		}



		/**
		 * @Route("/etudiant/importer_csv",name="etudiant_importer_csv")
		 *
		 */
		public function importerCsv(UserInterface $profResp,Etudiantsrepository $repoE, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, ProfesseursRepository $repoP, SecretaireRepository $repoS)
		{
				$classe=$this->getUser()->getClasseResponsable();
				if(isset($_POST['sub']) && $_FILES['importEtu']!=null){
						$file=$_FILES['importEtu'];
						$csv = Reader::createFromPath($file['tmp_name'], 'r');
						$csv->skipEmptyRecords();
						$csv->setHeaderOffset(0); //set the CSV header offset
						if(empty($csv->getHeader()) || count($csv)==0) {
								$this->addFlash('error','Erreur lors du chargement du fichier');
								$this->addFlash('info','Veuillez respecter la syntaxe : nom, prenom, mdp, mail, date');
								return $this->redirectToRoute("etudiant_rechercher");
						}
						foreach ($csv as $row)
						{
								try
								{
									$this->creerEtudiant($row['nom_etudiant'],$row['prenom_etudiant'],$row['mdp_etudiant'],$row['mail_etudiant'],
									new DateTime($row['date_naissance']),$repoE,$em,$encoder,$repoP,$repoS);
								}
								catch (\Exception $errorException)
								{
												$this->addFlash('error','Erreur lors du chargement du fichier');
												$this->addFlash('info','Veuillez respecter la syntaxe : nom, prenom, mdp, mail, date');
												return $this->redirectToRoute("etudiant_rechercher");
								}
						}
						$this->addFlash('success','La liste de '.count($csv).' étudiant(s) a bien été importée');
						return $this->redirectToRoute("etudiant_rechercher");
				}
				else
				{
                    return $this->redirectToRoute("etudiant_rechercher");
				}


		}


		public function creerEtudiant($nom,$prenom,$mdpEtudiant,$mail,$date, Etudiantsrepository $repoE, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, ProfesseursRepository $repoP, SecretaireRepository $repoS)
		{

				$etudiant = new Etudiants();
				$prenomLogin = strtolower($this->str_to_noaccent($prenom));
				$prenomLogin1 = substr($prenomLogin, 0,1);
				$login = strtolower($nom).$prenomLogin1;
				$mailAcademique = $prenomLogin.".".strtolower($nom);

				$i = "";
				$j = "";

				while($repoE->findBy(['login' => $login.$i]) || $repoP->findBy(['login' => $login.$i]) || $repoS->findBy(['login' => $login.$i]) )
				{
						if($i == "") $i = 0;
						$i++;
				}

				while($repoE->findBy(['mailAcademique' => $mailAcademique.$j."@etu.umontpellier.fr"]))
				{
						if($j == "") $j = 0;
						$j++;
				}


				$mail = strtolower($mail);
				$prenom = ucfirst(strtolower($prenom));
				$nom = strtoupper($nom);
				$hash = $encoder->encodePassword($etudiant,$mdpEtudiant);

				$etudiant->setMail($mail);
				$etudiant->setnom($nom);
				$etudiant->setPrenom($prenom);
				$etudiant->setLogin($login.$i);
				$etudiant->setMailAcademique($mailAcademique.$j."@etu.umontpellier.fr");
				$etudiant->setPassword($hash);
				$etudiant->setClasse($this->getUser()->getClasseResponsable());
				$etudiant->setdate_naissance($date);
				$em->persist($etudiant);
				$em->flush();
		}

}

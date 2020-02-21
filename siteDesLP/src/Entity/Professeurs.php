<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfesseursRepository")
 * @UniqueEntity("login",message="ce login est déjà utilisé")
 * @UniqueEntity("mailAcademique",message="ce mail académique est déjà utilisé")
 */
class Professeurs implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     *
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð,.'-]+$u/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $nomProfesseur;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un prénom")
     *
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð,.'-]+$u/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $prenomProfesseur;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $mailAcademique;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $login;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\Length(max = 64, min = 6, minMessage = "Mot de passe trop court, veuillez saisir un mot de passe d'au moins {{ limit }} caractères", maxMessage="Mot de passe trop long il est impossible d'avoir un mot de passe supérieur à {{ limit }} caractères")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    public $new_password;

    /**
     * @Assert\EqualTo(propertyPath="new_password", message="Vous n'avez pas tapé le même mot de passe !")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    private $passwordRequestedAt;


    /**
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $token;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classes", inversedBy="professeurs")
     */
    private $classes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Classes", mappedBy="professeurResponsable")
     */
    private $classeResponsable;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cours", mappedBy="prof", orphanRemoval=true)
     */
    private $dossiersCours;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StageForm", mappedBy="tuteurIUT")
     */
    private $stageForms;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->dossiersCours = new ArrayCollection();
        $this->stageForms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProfesseur(): ?string
    {
        return $this->nomProfesseur;
    }

    public function setNomProfesseur(string $nomProfesseur): self
    {
        $this->nomProfesseur = $nomProfesseur;

        return $this;
    }

    public function getPrenomProfesseur(): ?string
    {
        return $this->prenomProfesseur;
    }

    public function setPrenomProfesseur(string $prenomProfesseur): self
    {
        $this->prenomProfesseur = $prenomProfesseur;

        return $this;
    }

    public function getMailAcademique(): ?string
    {
        return $this->mailAcademique;
    }

    public function setMailAcademique(string $mailAcademique): self
    {
        $this->mailAcademique = $mailAcademique;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function getNewPassword(): ?string
    {
        return $this->new_password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        $this->new_password = "XXXXXX";
        $this->confirm_password = "XXXXXX";

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
      return $this->passwordRequestedAt;
    }

    public function getToken()
    {
      return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setPasswordRequestedAt($passwordRequestedAt)
   {
       $this->passwordRequestedAt = $passwordRequestedAt;
       return $this;
   }

    /**
     * @return Collection|Classes[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classes $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
        }

        return $this;
    }

    public function removeClass(Classes $class): self
    {
        if ($this->classes->contains($class)) {
            $this->classes->removeElement($class);
        }

        return $this;
    }

    public function getClasseResponsable(): ?Classes
    {
        return $this->classeResponsable;
    }

    public function setClasseResponsable(?Classes $classeResponsable): self
    {
        //$this->classeResponsable = $classeResponsable;

        // set (or unset) the owning side of the relation if necessary
        $newProfesseurResponsable = null === $classeResponsable ? null : $this;
        if($classeResponsable !== null) {
            if ($classeResponsable->getProfesseurResponsable() !== $newProfesseurResponsable) {
                $classeResponsable->setProfesseurResponsable($newProfesseurResponsable);
            }
        } else {
            $this->classeResponsable->setProfesseurResponsable(null);
        }

        $this->classeResponsable = $classeResponsable;

        return $this;
    }

    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
        if($this->getClasseResponsable() == null) return ['ROLE_PROFESSEUR'];
        else return ['ROLE_PROFESSEURRESPONSABLE'];
    }

    public function getUsername()
    {
      return $this->login;
    }

    public function __toString()
    {
        return $this->nomProfesseur." ".$this->prenomProfesseur;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getDossiersCours(): Collection
    {
        return $this->dossiersCours;
    }

    public function getDossiersRacinesCours(): Collection
    {
        $dossiersPrincipaux = [];
        foreach ($this->dossiersCours as $dossier)
            if($dossier->getCoursParent() == null)
                $dossiersPrincipaux[] = $dossier;
        return new ArrayCollection($dossiersPrincipaux);
    }

    public function addDossiersCour(Cours $dossiersCour): self
    {
        if (!$this->dossiersCours->contains($dossiersCour)) {
            $this->dossiersCours[] = $dossiersCour;
            $dossiersCour->setProf($this);
        }

        return $this;
    }

    public function removeDossiersCour(Cours $dossiersCour): self
    {
        if ($this->dossiersCours->contains($dossiersCour)) {
            $this->dossiersCours->removeElement($dossiersCour);
            // set the owning side to null (unless already changed)
            if ($dossiersCour->getProf() === $this) {
                $dossiersCour->setProf(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StageForm[]
     */
    public function getStageForms(): Collection
    {
        return $this->stageForms;
    }

    public function addStageForm(StageForm $stageForm): self
    {
        if (!$this->stageForms->contains($stageForm)) {
            $this->stageForms[] = $stageForm;
            $stageForm->setTuteurIUT($this);
        }

        return $this;
    }

    public function removeStageForm(StageForm $stageForm): self
    {
        if ($this->stageForms->contains($stageForm)) {
            $this->stageForms->removeElement($stageForm);
            // set the owning side to null (unless already changed)
            if ($stageForm->getTuteurIUT() === $this) {
                $stageForm->setTuteurIUT(null);
            }
        }

        return $this;
    }
}

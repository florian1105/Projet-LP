<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfesseursRepository")
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
     */
    private $prenomProfesseur;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un Email")
     *
     * @Assert\Email(message = "Veuillez saisir un mail valide s'il vous plait")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $mailAcademique;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un login")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $login;

    /**
     * @ORM\Column(type="date")
     * @Assert\Range(max="now", maxMessage="Ce Professeur ne peut pas être née avant aujourd'hui veuillez saisir une date valide")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un password")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classes", inversedBy="professeurs")
     */
    private $classes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Classes", mappedBy="professeurResponsable", cascade={"persist", "remove"})
     */
    private $classeResponsable;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
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

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
}

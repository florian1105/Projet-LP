<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

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
     */
    private $nomProfesseur;

    /**
     * @ORM\Column(type="string", length=64)
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
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=64)
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
        $this->classeResponsable = $classeResponsable;

        // set (or unset) the owning side of the relation if necessary
        $newProfesseurResponsable = null === $classeResponsable ? null : $this;
        if ($classeResponsable->getProfesseurResponsable() !== $newProfesseurResponsable) {
            $classeResponsable->setProfesseurResponsable($newProfesseurResponsable);
        }

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

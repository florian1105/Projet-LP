<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClassesRepository")
 * @UniqueEntity("nomClasse", message="Ce nom de classe est déjà utilisé, veuillez en saisir un autre")
 */
class Classes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $nomClasse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etudiants", mappedBy="classeEtudiant")
     */
    private $etudiants;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Professeurs", mappedBy="classes")
     */
    private $professeurs;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Professeurs", inversedBy="classeResponsable", cascade={"persist", "remove"})
     */
    private $professeurResponsable;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->professeurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClasse(): ?string
    {
        return $this->nomClasse;
    }

    public function setNomClasse(string $nomClasse): self
    {
        $this->nomClasse = $nomClasse;

        return $this;
    }

    /**
     * @return Collection|Etudiants[]
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiants $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->setClasseEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiants $etudiant): self
    {
        if ($this->etudiants->contains($etudiant)) {
            $this->etudiants->removeElement($etudiant);
            // set the owning side to null (unless already changed)
            if ($etudiant->getClasseEtudiant() === $this) {
                $etudiant->setClasseEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Professeurs[]
     */
    public function getProfesseurs(): Collection
    {
        return $this->professeurs;
    }

    public function addProfesseur(Professeurs $professeur): self
    {
        if (!$this->professeurs->contains($professeur)) {
            $this->professeurs[] = $professeur;
            $professeur->addClass($this);
        }

        return $this;
    }

    public function removeProfesseur(Professeurs $professeur): self
    {
        if ($this->professeurs->contains($professeur)) {
            $this->professeurs->removeElement($professeur);
            $professeur->removeClass($this);
        }

        return $this;
    }

    public function getProfesseurResponsable(): ?Professeurs
    {
        return $this->professeurResponsable;
    }

    public function setProfesseurResponsable(?Professeurs $professeurResponsable): self
    {
        $this->professeurResponsable = $professeurResponsable;

        return $this;
    }
}

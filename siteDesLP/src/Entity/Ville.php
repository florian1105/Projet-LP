<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VilleRepository")
 */
class Ville
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etudiants", mappedBy="ville")
     */
    private $etudiants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stage", mappedBy="ville")
     */
    private $stages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Entreprises", mappedBy="ville")
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprises", inversedBy="ville")
     */
    private $entreprises;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->stages = new ArrayCollection();
        $this->entreprise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
            $etudiant->setVille($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiants $etudiant): self
    {
        if ($this->etudiants->contains($etudiant)) {
            $this->etudiants->removeElement($etudiant);
            // set the owning side to null (unless already changed)
            if ($etudiant->getVille() === $this) {
                $etudiant->setVille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stage[]
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): self
    {
        if (!$this->stages->contains($stage)) {
            $this->stages[] = $stage;
            $stage->setVille($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->contains($stage)) {
            $this->stages->removeElement($stage);
            // set the owning side to null (unless already changed)
            if ($stage->getVille() === $this) {
                $stage->setVille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Entreprises[]
     */
    public function getEntreprise(): Collection
    {
        return $this->entreprise;
    }

    public function addEntreprise(Entreprises $entreprise): self
    {
        if (!$this->entreprise->contains($entreprise)) {
            $this->entreprise[] = $entreprise;
            $entreprise->setVille($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprises $entreprise): self
    {
        if ($this->entreprise->contains($entreprise)) {
            $this->entreprise->removeElement($entreprise);
            // set the owning side to null (unless already changed)
            if ($entreprise->getVille() === $this) {
                $entreprise->setVille(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->codePostal . ' ' . $this->nom;
    }

    public function getEntreprises(): ?Entreprises
    {
        return $this->entreprises;
    }

    public function setEntreprises(?Entreprises $entreprises): self
    {
        $this->entreprises = $entreprises;

        return $this;
    }
}

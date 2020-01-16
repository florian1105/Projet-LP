<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PromotionsRepository")
 * @UniqueEntity("annee",message="Cette promotion existe déjà")
 */
class Promotions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=9)
     * @Assert\Regex(pattern="/\d{4}\/\d{4}/", match=true, message="Le format de l'année doit être XXXX/XXXX")
     */
    private $annee;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classes", inversedBy="promotions")
     */
    private $classes;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Unique
     * @Assert\Range(
     *      min = 1900,
     *      max = 2899,
     *      minMessage = "La date début de la promotion doit au minimum être {{ limit }} ou plus.",
     *      maxMessage = "La date début de la promotion doit au maximum être {{ limit }} ou moins."
     * )
     */
    private $anneeDebut;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Unique
     * @Assert\Range(
     *      min = 1901,
     *      max = 2900,
     *      minMessage = "La date de fin de la promotion doit au minimum être {{ limit }} ou plus.",
     *      maxMessage = "La date de fin de la promotion doit au maximum être {{ limit }} ou moins."
     * )
     */
    private $anneeFin;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etudiants", mappedBy="promotion")
     */
    private $etudiants;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Candidats", mappedBy="promotions")
     */
    private $candidats;


    public function __construct()
    {
        $this->classe = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
        $this->candidats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getClasse(): Collection
    {
        return $this->classe;
    }

    public function addClasse(Classes $classe): self
    {
        if (!$this->classe->contains($classe)) {
            $this->classe[] = $classe;
            $classe->setPromotions($this);
        }

        return $this;
    }

    public function removeClasse(Classes $classe): self
    {
        if ($this->classe->contains($classe)) {
            $this->classe->removeElement($classe);
            // set the owning side to null (unless already changed)
            if ($classe->getPromotions() === $this) {
                $classe->setPromotions(null);
            }
        }

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

    public function getAnneeDebut(): ?int
    {
        return $this->anneeDebut;
    }

    public function setAnneeDebut(int $anneeDebut): self
    {
        $this->anneeDebut = $anneeDebut;

        return $this;
    }

    public function getAnneeFin(): ?int
    {
        return $this->anneeFin;
    }

    public function setAnneeFin(int $anneeFin): self
    {
        $this->anneeFin = $anneeFin;

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
            $etudiant->setPromotion($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiants $etudiant): self
    {
        if ($this->etudiants->contains($etudiant)) {
            $this->etudiants->removeElement($etudiant);
            // set the owning side to null (unless already changed)
            if ($etudiant->getPromotion() === $this) {
                $etudiant->setPromotion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Candidats[]
     */
    public function getCandidats(): Collection
    {
        return $this->candidats;
    }

    public function addCandidat(Candidats $candidat): self
    {
        if (!$this->candidats->contains($candidat)) {
            $this->candidats[] = $candidat;
            $candidat->addPromotion($this);
        }

        return $this;
    }

    public function removeCandidat(Candidats $candidat): self
    {
        if ($this->candidats->contains($candidat)) {
            $this->candidats->removeElement($candidat);
            $candidat->removePromotion($this);
        }

        return $this;
    }
}

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
 * @UniqueEntity("professeurResponsable", message="Ce professeur est déjà responsable d'une autre classe")
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
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Veuillez renseigner un nom pour cette classe")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés au début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a la fin.")
     * @Assert\Regex(pattern="/^[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés au début.")
     * @Assert\Regex(pattern="/[[:blank:]]$/", match=false, message="les espaces ne sont pas autorisés a la fin")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲@#▼&{}*$£%``¨^%+=.;,?\\'\x22]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $nomClasse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Utilisateurs", mappedBy="classe")
     */
    private $etudiants;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Professeurs", mappedBy="classes")
     */
    private $professeurs;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Professeurs", inversedBy="classeResponsable")
     * @ORM\JoinColumn(name="professeur_responsable_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Veuillez renseigner un professeur responsable")
     */
    private $professeurResponsable;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InformationsClasses", mappedBy="classe", cascade={"persist", "remove"})
     */
    private $informationsClasses;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $nomComplet;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Articles", mappedBy="classes")
     */
    private $articles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Cours", mappedBy="classes")
     */
    private $cours;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Offres", mappedBy="classes")
     */
    private $offres;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Promotions",fetch="EAGER",mappedBy="classes")
     */
    private $promotions;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->professeurs = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->offres = new ArrayCollection();
        $this->promotions = new ArrayCollection();
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

    public function isClasseResponsable()
    {
        if($this->professeurResponsable != null) return true;
        else return false;
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

    public function __toString()
    {
        return $this->nomClasse;
    }

    public function getInformationsClasses(): ?InformationsClasses
    {
        return $this->informationsClasses;
    }

    public function setInformationsClasses(InformationsClasses $informationsClasses): self
    {
        $this->informationsClasses = $informationsClasses;

        // set the owning side of the relation if necessary
        if ($informationsClasses->getClasse() !== $this) {
            $informationsClasses->setClasse($this);
        }

        return $this;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addClass($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            $article->removeClass($this);
        }

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->addClass($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            $cour->removeClass($this);
        }

        return $this;
    }

    public function getPromotions()
    {
        return $this->promotions;
    }

    public function setPromotions($promotions): self
    {
        $this->promotions = $promotions;

        return $this;
    }

    /**
     * @return Collection|Offres[]
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offres $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->addClass($this);
        }

        return $this;
    }

    public function removeOffre(Offres $offre): self
    {
        if ($this->offres->contains($offre)) {
            $this->offres->removeElement($offre);
            $offre->removeClass($this);
        }

        return $this;
    }

    public function addPromotion(Promotions $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->addClass($this);
        }

        return $this;
    }

    public function removePromotion(Promotions $promotion): self
    {
        if ($this->promotions->contains($promotion)) {
            $this->promotions->removeElement($promotion);
            $promotion->removeClass($this);
        }

        return $this;
    }


}

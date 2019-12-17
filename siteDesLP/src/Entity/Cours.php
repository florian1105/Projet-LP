<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoursRepository")
 * @UniqueEntity(fields={"nom", "coursParent"}, errorPath="nom", message="Il y a déjà un dossier {{ value }} dans ce dossier.")

 */
class Cours
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez renseigner un nom pour ce dossier")
     * @Assert\Length(max = 50, maxMessage="Nom de dossier/cours trop long il est impossible d'avoir un nom de dossier/cours supérieur à {{ limit }} caractères")
     */
    private $nom;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classes", inversedBy="cours")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Fichiers", mappedBy="cours", cascade={"remove"})
     */
    private $fichiers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cours", inversedBy="coursEnfants")
     */
    private $coursParent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cours", mappedBy="coursParent", cascade={"remove"})
     */
    private $coursEnfants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Professeurs", inversedBy="dossiersCours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prof;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->fichiers = new ArrayCollection();
        $this->coursEnfants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Fichiers[]
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichiers $fichier): self
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers[] = $fichier;
            $fichier->setCours($this);
        }

        return $this;
    }

    public function removeFichier(Fichiers $fichier): self
    {
        if ($this->fichiers->contains($fichier)) {
            $this->fichiers->removeElement($fichier);
            // set the owning side to null (unless already changed)
            if ($fichier->getCours() === $this) {
                $fichier->setCours(null);
            }
        }

        return $this;
    }

    public function getCoursParent(): ?self
    {
        return $this->coursParent;
    }

    public function setCoursParent(?self $coursParent): self
    {
        $this->coursParent = $coursParent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCoursEnfants(): Collection
    {
        return $this->coursEnfants;
    }

    public function addCoursEnfant(self $coursEnfant): self
    {
        if (!$this->coursEnfants->contains($coursEnfant)) {
            $this->coursEnfants[] = $coursEnfant;
            $coursEnfant->setCoursParent($this);
        }

        return $this;
    }

    public function removeCoursEnfant(self $coursEnfant): self
    {
        if ($this->coursEnfants->contains($coursEnfant)) {
            $this->coursEnfants->removeElement($coursEnfant);
            // set the owning side to null (unless already changed)
            if ($coursEnfant->getCoursParent() === $this) {
                $coursEnfant->setCoursParent(null);
            }
        }

        return $this;
    }

    public function getProf(): ?Professeurs
    {
        return $this->prof;
    }

    public function setProf(?Professeurs $prof): self
    {
        $this->prof = $prof;

        return $this;
    }
}

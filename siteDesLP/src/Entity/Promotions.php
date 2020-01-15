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


    public function __construct()
    {
        $this->classe = new ArrayCollection();
        $this->classes = new ArrayCollection();
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
}

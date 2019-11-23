<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InformationsClassesRepository")
 */
class InformationsClasses
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cheminPlaquette;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Classes", inversedBy="informationsClasses", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCheminPlaquette(): ?string
    {
        return $this->cheminPlaquette;
    }

    public function setCheminPlaquette(?string $cheminPlaquette): self
    {
        $this->cheminPlaquette = $cheminPlaquette;

        return $this;
    }

    public function getClasse(): ?Classes
    {
        return $this->classe;
    }

    public function setClasse(Classes $classe): self
    {
        $this->classe = $classe;

        return $this;
    }
}

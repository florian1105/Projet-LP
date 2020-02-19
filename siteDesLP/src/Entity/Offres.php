<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OffresRepository")
 */
class Offres
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Classes", inversedBy="offres")
     */
    private $classes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprises", inversedBy="offres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeOffre", inversedBy="offres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeOffre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contratAlternance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remuneration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateDuree;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailContact;


    public function __construct()
    {
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
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

    public function getEntreprise(): ?Entreprises
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprises $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeOffre(): ?TypeOffre
    {
        return $this->typeOffre;
    }

    public function setTypeOffre(?TypeOffre $typeOffre): self
    {
        $this->typeOffre = $typeOffre;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContratAlternance(): ?string
    {
        return $this->contratAlternance;
    }

    public function setContratAlternance(?string $contratAlternance): self
    {
        $this->contratAlternance = $contratAlternance;

        return $this;
    }

    public function getRemuneration(): ?string
    {
        return $this->remuneration;
    }

    public function setRemuneration(?string $remuneration): self
    {
        $this->remuneration = $remuneration;

        return $this;
    }

    public function getDateDuree(): ?string
    {
        return $this->dateDuree;
    }

    public function setDateDuree(?string $dateDuree): self
    {
        $this->dateDuree = $dateDuree;

        return $this;
    }

    public function getMailContact(): ?string
    {
        return $this->mailContact;
    }

    public function setMailContact(string $mailContact): self
    {
        $this->mailContact = $mailContact;

        return $this;
    }
}

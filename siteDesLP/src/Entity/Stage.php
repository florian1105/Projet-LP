<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StageRepository")
 */
class Stage
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
    private $sujet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ContactEntreprise", mappedBy="stages")
     */
    private $contactsEntreprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville", inversedBy="stages")
     */
    private $ville;

    public function __construct()
    {
        $this->contactsEntreprise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * @return Collection|ContactEntreprise[]
     */
    public function getContactsEntreprise(): Collection
    {
        return $this->contactsEntreprise;
    }

    public function addContactsEntreprise(ContactEntreprise $contactsEntreprise): self
    {
        if (!$this->contactsEntreprise->contains($contactsEntreprise)) {
            $this->contactsEntreprise[] = $contactsEntreprise;
            $contactsEntreprise->addStage($this);
        }

        return $this;
    }

    public function removeContactsEntreprise(ContactEntreprise $contactsEntreprise): self
    {
        if ($this->contactsEntreprise->contains($contactsEntreprise)) {
            $this->contactsEntreprise->removeElement($contactsEntreprise);
            $contactsEntreprise->removeStage($this);
        }

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}

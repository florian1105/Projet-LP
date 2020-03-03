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
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville", inversedBy="stages")
     */
    private $ville;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContactEntreprise", inversedBy="stages")
     * @ORM\JoinColumn(nullable=false, name="stage_id", referencedColumnName="id")}
     */
    private $tuteurEntreprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContactEntreprise", inversedBy="stages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $signataire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprises")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entreprise;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Etudiants", inversedBy="stage", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;


    public function __construct()
    {
        $this->tuteurEntreprise = new ArrayCollection();
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
    public function getTuteurEntrprise(): Collection
    {
        return $this->tuteurEntreprise;
    }

    public function addContactsEntreprise(ContactEntreprise $contactsEntreprise): self
    {
        if (!$this->tuteurEntreprise->contains($contactsEntreprise)) {
            $this->tuteurEntreprise[] = $contactsEntreprise;
            $contactsEntreprise->addStage($this);
        }

        return $this;
    }

    public function removeContactsEntreprise(ContactEntreprise $contactsEntreprise): self
    {
        if ($this->tuteurEntreprise->contains($contactsEntreprise)) {
            $this->tuteurEntreprise->removeElement($contactsEntreprise);
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

    public function getTuteurEntreprise(): ?ContactEntreprise
    {
        return $this->tuteurEntreprise;
    }

    public function setTuteurEntreprise(?ContactEntreprise $tuteurEntreprise): self
    {
        $this->tuteurEntreprise = $tuteurEntreprise;

        return $this;
    }

    public function getSignataire(): ?ContactEntreprise
    {
        return $this->signataire;
    }

    public function setSignataire(?ContactEntreprise $signataire): self
    {
        $this->signataire = $signataire;

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

    public function getEtudiant(): ?Etudiants
    {
        return $this->etudiant;
    }

    public function setEtudiant(Etudiants $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }
}

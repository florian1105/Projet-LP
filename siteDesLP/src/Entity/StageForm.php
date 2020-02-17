<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StageFormRepository")
 */
class StageForm
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
    private $numINE;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sex;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numeroTelEtudiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailPersoEtudiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomEntreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numSIRET;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addresseSiegeEntreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addresseStage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomPrenomSignataire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonctionSignataire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numTelSignataire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailSignataire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sujetStage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomTuteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomTuteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numTelTuteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailTuteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonctionTuteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $informationSupp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumINE(): ?string
    {
        return $this->numINE;
    }

    public function setNumINE(string $numINE): self
    {
        $this->numINE = $numINE;

        return $this;
    }

    public function getSex(): ?bool
    {
        return $this->sex;
    }

    public function setSex(bool $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getNumeroTelEtudiant(): ?string
    {
        return $this->numeroTelEtudiant;
    }

    public function setNumeroTelEtudiant(string $numeroTelEtudiant): self
    {
        $this->numeroTelEtudiant = $numeroTelEtudiant;

        return $this;
    }

    public function getMailPersoEtudiant(): ?string
    {
        return $this->mailPersoEtudiant;
    }

    public function setMailPersoEtudiant(string $mailPersoEtudiant): self
    {
        $this->mailPersoEtudiant = $mailPersoEtudiant;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getNumSIRET(): ?string
    {
        return $this->numSIRET;
    }

    public function setNumSIRET(string $numSIRET): self
    {
        $this->numSIRET = $numSIRET;

        return $this;
    }

    public function getAddresseSiegeEntreprise(): ?string
    {
        return $this->addresseSiegeEntreprise;
    }

    public function setAddresseSiegeEntreprise(string $addresseSiegeEntreprise): self
    {
        $this->addresseSiegeEntreprise = $addresseSiegeEntreprise;

        return $this;
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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAddresseStage(): ?string
    {
        return $this->addresseStage;
    }

    public function setAddresseStage(?string $addresseStage): self
    {
        $this->addresseStage = $addresseStage;

        return $this;
    }

    public function getNomPrenomSignataire(): ?string
    {
        return $this->nomPrenomSignataire;
    }

    public function setNomPrenomSignataire(string $nomPrenomSignataire): self
    {
        $this->nomPrenomSignataire = $nomPrenomSignataire;

        return $this;
    }

    public function getFonctionSignataire(): ?string
    {
        return $this->fonctionSignataire;
    }

    public function setFonctionSignataire(string $fonctionSignataire): self
    {
        $this->fonctionSignataire = $fonctionSignataire;

        return $this;
    }

    public function getNumTelSignataire(): ?string
    {
        return $this->numTelSignataire;
    }

    public function setNumTelSignataire(string $numTelSignataire): self
    {
        $this->numTelSignataire = $numTelSignataire;

        return $this;
    }

    public function getMailSignataire(): ?string
    {
        return $this->mailSignataire;
    }

    public function setMailSignataire(string $mailSignataire): self
    {
        $this->mailSignataire = $mailSignataire;

        return $this;
    }

    public function getSujetStage(): ?string
    {
        return $this->sujetStage;
    }

    public function setSujetStage(string $sujetStage): self
    {
        $this->sujetStage = $sujetStage;

        return $this;
    }

    public function getNomTuteur(): ?string
    {
        return $this->nomTuteur;
    }

    public function setNomTuteur(string $nomTuteur): self
    {
        $this->nomTuteur = $nomTuteur;

        return $this;
    }

    public function getPrenomTuteur(): ?string
    {
        return $this->prenomTuteur;
    }

    public function setPrenomTuteur(string $prenomTuteur): self
    {
        $this->prenomTuteur = $prenomTuteur;

        return $this;
    }

    public function getNumTelTuteur(): ?string
    {
        return $this->numTelTuteur;
    }

    public function setNumTelTuteur(string $numTelTuteur): self
    {
        $this->numTelTuteur = $numTelTuteur;

        return $this;
    }

    public function getMailTuteur(): ?string
    {
        return $this->mailTuteur;
    }

    public function setMailTuteur(string $mailTuteur): self
    {
        $this->mailTuteur = $mailTuteur;

        return $this;
    }

    public function getFonctionTuteur(): ?string
    {
        return $this->fonctionTuteur;
    }

    public function setFonctionTuteur(string $fonctionTuteur): self
    {
        $this->fonctionTuteur = $fonctionTuteur;

        return $this;
    }

    public function getInformationSupp(): ?string
    {
        return $this->informationSupp;
    }

    public function setInformationSupp(string $informationSupp): self
    {
        $this->informationSupp = $informationSupp;

        return $this;
    }
}

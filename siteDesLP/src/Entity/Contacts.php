<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @UniqueEntity("mail",message="ce mail est déjà utilisé")
 * @UniqueEntity("telephone",message="ce numéro de tel est déjà utilisé")
 */
class Contacts{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     *
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲@#▼&{}*$£%``¨^%+=.;,?\\'\x22]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     *
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲@#▼&{}*$£%``¨^%+=.;,?\\'\x22]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un mail")
     * @Assert\Email(message = "Veuillez saisir un mail valide s'il vous plait")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=10)
     *
     * @Assert\NotNull(message="Contact doit avoir un numéro de télephone")
     * @Assert\Regex(pattern="/[[:digit:]]/", match=true, message="Seuls les chiffres sont autorisés")
     *
     */
    private $telephone;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprises")
     * @Assert\NotNull(message="Contact doit avoir une entreprise.")
     */
    private $entreprise;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * @param mixed $entreprise
     */
    public function setEntreprise($entreprise): void
    {
        $this->entreprise = $entreprise;
    }

    public function getValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }


}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateursRepository")
 * @InheritanceType("JOINED")
 * @ORM\Table(name="utilisateurs")
 * @UniqueEntity("mail", message="ce mail est déjà utilisé")
 *
 */
class Utilisateurs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=24)
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð,.'-]+$u/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=24)
     * @Assert\NotBlank(message="Veuillez renseigner un prénom")
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés au début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a la fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð,.'-]+$u/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
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
     * @ORM\Column(type="string", length=255)
     *
     */
    private $password;

    /**
     * @Assert\Length(max = 64, min = 6, minMessage = "Mot de passe trop court, veuillez saisir un mot de passe d'au moins {{ limit }} caractères", maxMessage="Mot de passe trop long il est impossible d'avoir un mot de passe supérieur à {{ limit }} caractères")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    public $new_password;

    /**
     * @Assert\EqualTo(propertyPath="new_password", message="Vous n'avez pas tapé le même mot de passe !")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="date")
     * @Assert\Range(max="now", maxMessage="Cet étudiant ne peut pas être née avant aujourd'hui veuillez saisir une date valide")
     */
    private $date_naissance;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classes", inversedBy="etudiants")
     * @ORM\JoinColumn(name="classe_utilisateur_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $classe;

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


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        $this->new_password = "XXXXXX";
        $this->confirm_password = "XXXXXX";
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateNaissance()
    {
        return $this->date_naissance;
    }


    public function setDateNaissance(\DateTime $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }
    public function getMail()
    {
        return $this->mail;
    }

    public function setMail(String $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getNewPassword()
    {
        return $this->new_password;
    }


    public function setNewPassword($new_password): void
    {
        $this->new_password = $new_password;
    }

    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword($confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    public function getClasse(): ?Classes
    {
        return $this->classe;
    }

    public function setClasse(?Classes $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

}

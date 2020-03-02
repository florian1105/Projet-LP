<?php

namespace App\Entity;
//TODO: Mettre le nom au pluriels (ajoute un "s" boloss)
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\SecretaireRepository")
 * @UniqueEntity("login",message="ce login est déjà utilisé")
 * @UniqueEntity("mailAcademique",message="ce mail académique est déjà utilisé")
 */
class Secretaire implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     *
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð,.'-]+$u/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $nomSecretaire;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     *
     * @Assert\Regex(pattern="/[[:digit:]]/", match=false, message="Les chiffres ne sont pas autorisés")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[[:blank:]]/", match=false, message="les espaces ne sont pas autorisés")
     * @Assert\Regex(pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð,.'-]+$u/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $prenomSecretaire;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $mailAcademique;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\Length(max = 64, min = 6, minMessage = "Mot de passe trop court, veuillez saisir un mot de passe d'au moins {{ limit }} caractères", maxMessage="Mot de passe trop long il est impossible d'avoir un mot de passe supérieur à {{ limit }} caractères")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲▼]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $new_password;

    /**
     * @Assert\EqualTo(propertyPath="new_password", message="Vous n'avez pas tapé le même mot de passe !")
     */
    private $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    private $passwordRequestedAt;


    /**
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $token;


    public function getNomSecretaire(): ?string
    {
        return $this->nomSecretaire;
    }

    public function setNomSecretaire(string $nomSecretaire): self
    {
        $this->nomSecretaire = $nomSecretaire;

        return $this;
    }

    public function getPrenomSecretaire(): ?string
    {
        return $this->prenomSecretaire;
    }

    public function setPrenomSecretaire(string $prenomSecretaire): self
    {
        $this->prenomSecretaire = $prenomSecretaire;

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

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
      return $this->passwordRequestedAt;
    }

    public function getToken()
    {
      return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setPasswordRequestedAt($passwordRequestedAt)
   {
       $this->passwordRequestedAt = $passwordRequestedAt;
       return $this;
   }


    public function getNewPassword(): ?string
    {
        return $this->new_password;
    }

    public function setNewPassword(string $new_password): self
    {
        $this->new_password = $new_password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login): void
    {
        $this->login = $login;
    }


    public function getMailAcademique()
    {
        return $this->mailAcademique;
    }

    public function setMailAcademique($mailAcademique): void
    {
        $this->mailAcademique = $mailAcademique;
    }

    public function getContactMail()
    {
      return $this->mailAcademique;
    }


    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
        return ['ROLE_SECRETAIRE'];
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function __toString()
    {
        return $this->nomSecretaire." ".$this->prenomSecretaire;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @UniqueEntity("mail",message="ce mail est déjà utilisé")
 * @UniqueEntity("telephone",message="ce numéro de tel est déjà utilisé")
 */
class Contacts implements UserInterface {
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
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    private $passwordRequestedAt;


    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;


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


    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): Utilisateurs
    {
        $this->password=$password;
    }

    public function getNewPassword(): ?string
    {
        return $this->new_password;
    }

    public function setNewPassword($new_password): void
    {
        $this->new_password=$new_password;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }
    public function getConfirmPassword()
    {
        return $this->getConfirmPassword();
    }
    public function setConfirmPassword($confirm_password): void
    {
        $this->confirm_password=$confirm_password;
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




    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}

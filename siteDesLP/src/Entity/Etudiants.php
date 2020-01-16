<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\EtudiantsRepository")
 * @UniqueEntity("login",message="ce login est déjà utilisé")
 * @UniqueEntity("mailAcademique",message="ce mail académique est déjà utilisé")
 */
class Etudiants extends Utilisateurs implements UserInterface
{


    /**
     * @ORM\Column(type="string", length=64)
     */
    private $mailAcademique;



    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;


    /**
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $token;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $login;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promotions", inversedBy="etudiants")
     */
    private $promotion;

    public function getNomEtudiant(): ?string
    {
        return parent::getNom();
    }

    public function setNomEtudiant(string $nomEtudiant): Utilisateurs
    {
        return parent::setNom($nomEtudiant);
    }

    public function getPrenomEtudiant(): ?string
    {
        return parent::getPrenom();
    }

    public function setPrenomEtudiant(string $prenomEtudiant): Utilisateurs
    {
        return parent::setPrenom($prenomEtudiant);
    }

    public function getMailAcademique(): ?string
    {
        return $this->mailAcademique;
    }

    public function setMailAcademique(string $mailAcademique): self
    {
        $this->mailAcademique = $mailAcademique;

        return $this;
    }

    public function getMail(): ?string
    {
        return parent::getMail();
    }

    public function setMail(String $mail): Utilisateurs
    {
        return parent::setMail($mail);
    }

    public function getPassword(): ?string
    {
        return parent::getPassword();
    }

    public function setPassword(string $password): Utilisateurs
    {
        return parent::setPassword($password);
    }

    public function getNewPassword(): ?string
    {
        return parent::getNewPassword();
    }

    public function setNewPassword($new_password): void
    {
        parent::setNewPassword($new_password);
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
      return $this->passwordRequestedAt;
    }

    public function setConfirmPassword($confirm_password): void
    {
        parent::setConfirmPassword($confirm_password);
    }

    public function getConfirmPassword()
    {
        return parent::getConfirmPassword();
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return parent::getDateNaissance();
    }


    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
      return ['ROLE_ETUDIANT'];
    }

    public function getUsername()
    {
      return $this->login;
    }

    public function __toString()
    {
        return parent::getNom()." ".parent::getPrenom();
    }

    public function getPromotion(): ?Promotions
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotions $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

}

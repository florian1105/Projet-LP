<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidatsRepository")
 *
 */
class Candidats extends Utilisateurs implements UserInterface
{
    public function getId(): ?int
    {
        return parent::getId();
    }

    public function getNom(): ?string
    {
        return parent::getNom();
    }

    public function setNom(string $nom): Utilisateurs
    {
        return parent::setNom($nom);
    }

    public function getPrenom(): ?string
    {
        return parent::getPrenom();
    }

    public function setPrenom(string $prenom): Utilisateurs
    {
        return parent::setPrenom($prenom);
    }

    public function getPassword(): ?string
    {
        return parent::getPassword();
    }

    public function setPassword(string $password): Utilisateurs
    {
        return parent::setPassword($password);
    }

    public function getNewPassword()
    {
        return parent::getNewPassword();
    }

    public function setNewPassword($new_password): void
    {
        parent::setNewPassword($new_password);
    }

    public function getConfirmPassword()
    {
        return parent::getConfirmPassword();
    }

    public function setConfirmPassword($confirm_password): void
    {
        parent::setConfirmPassword($confirm_password);
    }

    public function getDateNaissance()
    {
        return parent::getDateNaissance();
    }

    public function setDateNaissance(\DateTime $date_naissance): Utilisateurs
    {
        return parent::setDateNaissance($date_naissance);
    }

    public function getMail()
    {
        return parent::getMail();
    }
    public function setMail(String $mail): Utilisateurs
    {
        return parent::setMail($mail);
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResponsableDesStagesRepository")
 */
class ResponsableDesStages implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Professeurs", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $idProfesseur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
        return ['ROLE_RESPONSABLE_DES_STAGES'];
    }


    public function getIdProfesseur(): ?Professeurs
    {
        return $this->idProfesseur;
    }

    public function setIdProfesseur(Professeurs $idProfesseur): self
    {
        $this->idProfesseur = $idProfesseur;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }
}

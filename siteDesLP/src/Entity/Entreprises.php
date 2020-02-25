<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EntreprisesRepository")
 * @UniqueEntity("nom",message="ce nom est déjà utilisé")
 */
class Entreprises
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",name="id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     * @Assert\Regex(pattern="/^-/", match=false, message="les - ne sont pas autorisés a début.")
     * @Assert\Regex(pattern="/-$/", match=false, message="les - ne sont pas autorisés a fin.")
     * @Assert\Regex(pattern="/[☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←∟↔▲@#▼&{}*$£%``¨^%+=.;,!?\\'\x]/", match=false, message="les caractéres spéciaux ne sont pas autorisés")
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contacts" , mappedBy="entreprise")
     *
     */
    private $contactEntreprise;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Offres", mappedBy="entreprise")
     */
    private $offres;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville", inversedBy="entreprise")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $numSiret;


    public function __toString()
    {
        return $this->nom;
    }

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

    /**
     * @return mixed
     */
    public function getContactEntreprise()
    {
        return $this->contactEntreprise;
    }

    /**
     * @param mixed $contactEntreprise
     */
    public function setContactEntreprise(Contacts $contactEntreprise): void
    {
        $this->contactEntreprise = $contactEntreprise;
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
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_ENTREPRISE'];
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
     * @return Collection|Offres[]
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offres $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->setEntreprise($this);
        }

        return $this;
    }

    public function removeOffre(Offres $offre): self
    {
        if ($this->offres->contains($offre)) {
            $this->offres->removeElement($offre);
            // set the owning side to null (unless already changed)
            if ($offre->getEntreprise() === $this) {
                $offre->setEntreprise(null);
            }
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

    public function getNumSiret(): ?string
    {
        return $this->numSiret;
    }

    public function setNumSiret(?string $numSiret): self
    {
        $this->numSiret = $numSiret;

        return $this;
    }
}

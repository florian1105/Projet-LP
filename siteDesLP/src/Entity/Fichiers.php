<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FichiersRepository")
 * )
 */
class Fichiers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(maxSize="20M",maxSizeMessage ="Ce fichier est trop volumineux. Veuillez en envoyer un plus petit.", mimeTypes = {"application/*","fonts/*", "text/*"}, mimeTypesMessage = "Impossible d'envoyer ce format de fichier. Veuillez envoyer un fichier texte ou code.")
     */
    private $emplacement;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cours", inversedBy="fichiers")
     */
    private $cours;

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

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    // Gestions des fichiers du formulaire
    private $formFichiers;
    public function getFormFichiers(): ?array
    {
        return $this->formFichiers;
    }

    public function setFormFichiers(?array $formFichiers): ?self
    {
        $this->formFichiers = $formFichiers;

        return $this;
    }
}

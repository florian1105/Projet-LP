<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtatStageRepository")
 */
class EtatStage
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
    private $nomEtat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StageForm", mappedBy="etatStages")
     */
    private $stage;



    /**
     * @return mixed
     */
    public function getStageForm()
    {
        return $this->stageForm;
    }

    /**
     * @param mixed $stageForm
     */
    public function setStageForm($stageForm): void
    {
        $this->stageForm = $stageForm;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEtat(): ?string
    {
        return $this->nomEtat;
    }

    public function setNomEtat(string $nomEtat): self
    {
        $this->nomEtat = $nomEtat;

        return $this;
    }

    public function getStage(): ?StageForm
    {
        return $this->stage;
    }

    public function setStage(?StageForm $stage): self
    {
        $this->stage = $stage;

        return $this;
    }
}

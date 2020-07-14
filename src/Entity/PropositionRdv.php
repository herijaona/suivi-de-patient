<?php

namespace App\Entity;

use App\Repository\PropositionRdvRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropositionRdvRepository::class)
 */
class PropositionRdv
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateProposition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionProposition;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statusProposition;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="propositionRdvs")
     */
    private $praticien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateProposition(): ?\DateTimeInterface
    {
        return $this->dateProposition;
    }

    public function setDateProposition(\DateTimeInterface $dateProposition): self
    {
        $this->dateProposition = $dateProposition;

        return $this;
    }

    public function getDescriptionProposition(): ?string
    {
        return $this->descriptionProposition;
    }

    public function setDescriptionProposition(string $descriptionProposition): self
    {
        $this->descriptionProposition = $descriptionProposition;

        return $this;
    }

    public function getStatusProposition(): ?int
    {
        return $this->statusProposition;
    }

    public function setStatusProposition(?int $statusProposition): self
    {
        $this->statusProposition = $statusProposition;

        return $this;
    }

    public function getPraticien(): ?Praticien
    {
        return $this->praticien;
    }

    public function setPraticien(?Praticien $praticien): self
    {
        $this->praticien = $praticien;

        return $this;
    }
}

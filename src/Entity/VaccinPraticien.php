<?php

namespace App\Entity;

use App\Repository\VaccinPraticienRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VaccinPraticienRepository::class)
 */
class VaccinPraticien
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="vaccinPraticiens")
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="vaccinPraticiens")
     */
    private $vaccin;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVaccin(): ?Vaccin
    {
        return $this->vaccin;
    }

    public function setVaccin(?Vaccin $vaccin): self
    {
        $this->vaccin = $vaccin;

        return $this;
    }
}

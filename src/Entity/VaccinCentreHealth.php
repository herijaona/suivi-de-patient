<?php

namespace App\Entity;

use App\Repository\VaccinCentreHealthRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VaccinCentreHealthRepository::class)
 */
class VaccinCentreHealth
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CentreHealth::class, inversedBy="vaccinCentreHealths")
     */
    private $centreHealth;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="vaccinCentreHealths")
     */
    private $vaccin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCentreHealth(): ?CentreHealth
    {
        return $this->centreHealth;
    }

    public function setCentreHealth(?CentreHealth $centreHealth): self
    {
        $this->centreHealth = $centreHealth;

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

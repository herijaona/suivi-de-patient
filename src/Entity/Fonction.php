<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FonctionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=FonctionRepository::class)
 */
class Fonction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonction;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="fonctions")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="fonctions")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="fonctions")
     */
    private $Praticien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPraticien(): ?Praticien
    {
        return $this->Praticien;
    }

    public function setPraticien(?Praticien $Praticien): self
    {
        $this->Praticien = $Praticien;

        return $this;
    }
}

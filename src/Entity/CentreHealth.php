<?php

namespace App\Entity;

use App\Repository\CentreHealthRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CentreHealthRepository::class)
 */
class CentreHealth
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
    private $centre_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $centre_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $centre_referent;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="centreHealths")
     */
    private $centre_city;

    /**
     * @ORM\ManyToOne(targetEntity=CentreType::class, inversedBy="centreHealths")
     */
    private $centre_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCentreName(): ?string
    {
        return $this->centre_name;
    }

    public function setCentreName(string $centre_name): self
    {
        $this->centre_name = $centre_name;

        return $this;
    }

    public function getCentrePhone(): ?string
    {
        return $this->centre_phone;
    }

    public function setCentrePhone(string $centre_phone): self
    {
        $this->centre_phone = $centre_phone;

        return $this;
    }

    public function getCentreReferent(): ?string
    {
        return $this->centre_referent;
    }

    public function setCentreReferent(?string $centre_referent): self
    {
        $this->centre_referent = $centre_referent;

        return $this;
    }

    public function getCentreCity(): ?City
    {
        return $this->centre_city;
    }

    public function setCentreCity(?City $centre_city): self
    {
        $this->centre_city = $centre_city;

        return $this;
    }

    public function getCentreType(): ?CentreType
    {
        return $this->centre_type;
    }

    public function setCentreType(?CentreType $centre_type): self
    {
        $this->centre_type = $centre_type;

        return $this;
    }
}

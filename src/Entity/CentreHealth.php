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
    private $centreName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $centrePhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $centreReferent;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="centreHealths")
     */
    private $centreCity;

    /**
     * @ORM\ManyToOne(targetEntity=CentreType::class, inversedBy="centreHealths")
     */
    private $centreType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCentreName(): ?string
    {
        return $this->centreName;
    }

    public function setCentreName(string $centreName): self
    {
        $this->centreName = $centreName;

        return $this;
    }

    public function getCentrePhone(): ?string
    {
        return $this->centrePhone;
    }

    public function setCentrePhone(string $centrePhone): self
    {
        $this->centrePhone = $centrePhone;

        return $this;
    }

    public function getCentreReferent(): ?string
    {
        return $this->centreReferent;
    }

    public function setCentreReferent(?string $centreReferent): self
    {
        $this->centreReferent = $centreReferent;

        return $this;
    }

    public function getCentreCity(): ?City
    {
        return $this->centreCity;
    }

    public function setCentreCity(?City $centreCity): self
    {
        $this->centreCity = $centreCity;

        return $this;
    }

    public function getCentreType(): ?CentreType
    {
        return $this->centreType;
    }

    public function setCentreType(?CentreType $centreType): self
    {
        $this->centreType = $centreType;

        return $this;
    }
}

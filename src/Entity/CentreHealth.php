<?php

namespace App\Entity;

use App\Repository\CentreHealthRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsableCentre;

    /**
     * @ORM\OneToMany(targetEntity=VaccinCentreHealth::class, mappedBy="centreHealth")
     */
    private $vaccinCentreHealths;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="centerHealth")
     */
    private $address;

    public function __construct()
    {
        $this->vaccinCentreHealths = new ArrayCollection();
    }

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

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(?int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getResponsableCentre(): ?string
    {
        return $this->responsableCentre;
    }

    public function setResponsableCentre(?string $responsableCentre): self
    {
        $this->responsableCentre = $responsableCentre;

        return $this;
    }

    /**
     * @return Collection|VaccinCentreHealth[]
     */
    public function getVaccinCentreHealths(): Collection
    {
        return $this->vaccinCentreHealths;
    }

    public function addVaccinCentreHealth(VaccinCentreHealth $vaccinCentreHealth): self
    {
        if (!$this->vaccinCentreHealths->contains($vaccinCentreHealth)) {
            $this->vaccinCentreHealths[] = $vaccinCentreHealth;
            $vaccinCentreHealth->setCentreHealth($this);
        }

        return $this;
    }

    public function removeVaccinCentreHealth(VaccinCentreHealth $vaccinCentreHealth): self
    {
        if ($this->vaccinCentreHealths->contains($vaccinCentreHealth)) {
            $this->vaccinCentreHealths->removeElement($vaccinCentreHealth);
            // set the owning side to null (unless already changed)
            if ($vaccinCentreHealth->getCentreHealth() === $this) {
                $vaccinCentreHealth->setCentreHealth(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }
}

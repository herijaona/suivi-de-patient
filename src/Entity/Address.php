<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AddressRepository::class)
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="addresses")
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $numRue;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $Quartier;

    /**
     * @ORM\OneToMany(targetEntity=CentreHealth::class, mappedBy="address")
     */
    private $centerHealth;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="address")
     */
    private $praticien;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="address")
     */
    private $patient;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="addressOnBorn")
     */
    private $patients;

    public function __construct()
    {
        $this->centerHealth = new ArrayCollection();
        $this->praticien = new ArrayCollection();
        $this->patient = new ArrayCollection();
        $this->patients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVille(): ?City
    {
        return $this->ville;
    }

    public function setVille(?City $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getNumRue(): ?string
    {
        return $this->numRue;
    }

    public function setNumRue(?string $numRue): self
    {
        $this->numRue = $numRue;

        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->Quartier;
    }

    public function setQuartier(string $Quartier): self
    {
        $this->Quartier = $Quartier;

        return $this;
    }

    /**
     * @return Collection|CentreHealth[]
     */
    public function getCenterHealth(): Collection
    {
        return $this->centerHealth;
    }

    public function addCenterHealth(CentreHealth $centerHealth): self
    {
        if (!$this->centerHealth->contains($centerHealth)) {
            $this->centerHealth[] = $centerHealth;
            $centerHealth->setAddress($this);
        }

        return $this;
    }

    public function removeCenterHealth(CentreHealth $centerHealth): self
    {
        if ($this->centerHealth->contains($centerHealth)) {
            $this->centerHealth->removeElement($centerHealth);
            // set the owning side to null (unless already changed)
            if ($centerHealth->getAddress() === $this) {
                $centerHealth->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getPraticien(): Collection
    {
        return $this->praticien;
    }

    public function addPraticien(Praticien $praticien): self
    {
        if (!$this->praticien->contains($praticien)) {
            $this->praticien[] = $praticien;
            $praticien->setAddress($this);
        }

        return $this;
    }

    public function removePraticien(Praticien $praticien): self
    {
        if ($this->praticien->contains($praticien)) {
            $this->praticien->removeElement($praticien);
            // set the owning side to null (unless already changed)
            if ($praticien->getAddress() === $this) {
                $praticien->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatient(): Collection
    {
        return $this->patient;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patient->contains($patient)) {
            $this->patient[] = $patient;
            $patient->setAddress($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patient->contains($patient)) {
            $this->patient->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getAddress() === $this) {
                $patient->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }
}

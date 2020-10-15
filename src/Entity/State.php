<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:state"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"} 
 * )
 */
class State
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:state", "read:patient"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:state"})
     */
    private $nameState;

    /**
     * @ORM\OneToMany(targetEntity=Region::class, mappedBy="state", orphanRemoval=true)
     * @Groups({"read:state"})
     */
    private $regions;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="state")
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="state")
     */
    private $praticiens;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneindic;

    /**
     * @ORM\OneToMany(targetEntity=Vaccin::class, mappedBy="state")
     */
    private $vaccins;

    /**
     * @ORM\OneToMany(targetEntity=Fonction::class, mappedBy="state")
     */
    private $fonctions;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="CountryOnborn")
     */
    private $patient;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="CountryOnBorn")
     */
    private $praticienonborn;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="CountryFonction")
     */
    private $praticien;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->praticiens = new ArrayCollection();
        $this->vaccins = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
        $this->patient = new ArrayCollection();
        $this->praticienonborn = new ArrayCollection();
        $this->praticien = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameState(): ?string
    {
        return $this->nameState;
    }

    public function setNameState(string $name_state): self
    {
        $this->nameState = $name_state;

        return $this;
    }

    /**
     * @return Collection|Region[]
     */
    public function getRegions(): Collection
    {
        return $this->regions;
    }

    public function addRegion(Region $region): self
    {
        if (!$this->regions->contains($region)) {
            $this->regions[] = $region;
            $region->setState($this);
        }

        return $this;
    }

    public function removeRegion(Region $region): self
    {
        if ($this->regions->contains($region)) {
            $this->regions->removeElement($region);
            // set the owning side to null (unless already changed)
            if ($region->getState() === $this) {
                $region->setState(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNameState();
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setState($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getState() === $this) {
                $patient->setState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getPraticiens(): Collection
    {
        return $this->praticiens;
    }

    public function addPraticien(Praticien $praticien): self
    {
        if (!$this->praticiens->contains($praticien)) {
            $this->praticiens[] = $praticien;
            $praticien->setState($this);
        }

        return $this;
    }

    public function removePraticien(Praticien $praticien): self
    {
        if ($this->praticiens->contains($praticien)) {
            $this->praticiens->removeElement($praticien);
            // set the owning side to null (unless already changed)
            if ($praticien->getState() === $this) {
                $praticien->setState(null);
            }
        }

        return $this;
    }

    public function getPhoneindic(): ?string
    {
        return $this->phoneindic;
    }

    public function setPhoneindic(?string $phoneindic): self
    {
        $this->phoneindic = $phoneindic;

        return $this;
    }

    /**
     * @return Collection|Vaccin[]
     */
    public function getVaccins(): Collection
    {
        return $this->vaccins;
    }

    public function addVaccin(Vaccin $vaccin): self
    {
        if (!$this->vaccins->contains($vaccin)) {
            $this->vaccins[] = $vaccin;
            $vaccin->setState($this);
        }

        return $this;
    }

    public function removeVaccin(Vaccin $vaccin): self
    {
        if ($this->vaccins->contains($vaccin)) {
            $this->vaccins->removeElement($vaccin);
            // set the owning side to null (unless already changed)
            if ($vaccin->getState() === $this) {
                $vaccin->setState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fonction[]
     */
    public function getFonctions(): Collection
    {
        return $this->fonctions;
    }

    public function addFonction(Fonction $fonction): self
    {
        if (!$this->fonctions->contains($fonction)) {
            $this->fonctions[] = $fonction;
            $fonction->setState($this);
        }

        return $this;
    }

    public function removeFonction(Fonction $fonction): self
    {
        if ($this->fonctions->contains($fonction)) {
            $this->fonctions->removeElement($fonction);
            // set the owning side to null (unless already changed)
            if ($fonction->getState() === $this) {
                $fonction->setState(null);
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

    /**
     * @return Collection|Praticien[]
     */
    public function getPraticienonborn(): Collection
    {
        return $this->praticienonborn;
    }

    public function addPraticienonborn(Praticien $praticienonborn): self
    {
        if (!$this->praticienonborn->contains($praticienonborn)) {
            $this->praticienonborn[] = $praticienonborn;
            $praticienonborn->setCountryOnBorn($this);
        }

        return $this;
    }

    public function removePraticienonborn(Praticien $praticienonborn): self
    {
        if ($this->praticienonborn->contains($praticienonborn)) {
            $this->praticienonborn->removeElement($praticienonborn);
            // set the owning side to null (unless already changed)
            if ($praticienonborn->getCountryOnBorn() === $this) {
                $praticienonborn->setCountryOnBorn(null);
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


}

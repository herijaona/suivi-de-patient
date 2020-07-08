<?php

namespace App\Entity;

use App\Repository\VaccinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VaccinRepository::class)
 */
class Vaccin
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
    private $vaccinName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vaccinDescription;

    /**
     * @ORM\ManyToOne(targetEntity=TypeVaccin::class, inversedBy="vaccins")
     */
    private $TypeVaccin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;


    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="vaccin")
     */
    private $interventionVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoVaccination::class, mappedBy="vaccin")
     */
    private $ordoVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=CarnetVaccination::class, mappedBy="vaccin")
     */
    private $carnetVaccinations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $datePriseInitiale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel5;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel6;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel7;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel8;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel9;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappel10;

    /**
     * @ORM\OneToMany(targetEntity=PatientVaccin::class, mappedBy="vaccin")
     */
    private $patientVaccins;

    /**
     * @ORM\OneToMany(targetEntity=VaccinCentreHealth::class, mappedBy="vaccin")
     */
    private $vaccinCentreHealths;

    /**
     * @ORM\OneToMany(targetEntity=VaccinPraticien::class, mappedBy="vaccin")
     */
    private $vaccinPraticiens;

    function __construct()
    {
        $this->etat = false;
        $this->interventionVaccinations = new ArrayCollection();
        $this->ordoVaccinations = new ArrayCollection();
        $this->carnetVaccinations = new ArrayCollection();
        $this->patientVaccins = new ArrayCollection();
        $this->vaccinCentreHealths = new ArrayCollection();
        $this->vaccinPraticiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVaccinName(): ?string
    {
        return $this->vaccinName;
    }

    public function setVaccinName(string $vaccin_name): self
    {
        $this->vaccinName = $vaccin_name;

        return $this;
    }

    public function getVaccinDescription(): ?string
    {
        return $this->vaccinDescription;
    }

    public function setVaccinDescription(?string $vaccin_description): self
    {
        $this->vaccinDescription = $vaccin_description;

        return $this;
    }

    public function getTypeVaccin(): ?TypeVaccin
    {
        return $this->TypeVaccin;
    }

    public function setTypeVaccin(?TypeVaccin $Type_vaccin): self
    {
        $this->TypeVaccin = $Type_vaccin;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|InterventionVaccination[]
     */
    public function getInterventionVaccinations(): Collection
    {
        return $this->interventionVaccinations;
    }

    public function addInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if (!$this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations[] = $interventionVaccination;
            $interventionVaccination->setVaccin($this);
        }

        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getVaccin() === $this) {
                $interventionVaccination->setVaccin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdoVaccination[]
     */
    public function getOrdoVaccinations(): Collection
    {
        return $this->ordoVaccinations;
    }

    public function addOrdoVaccination(OrdoVaccination $ordoVaccination): self
    {
        if (!$this->ordoVaccinations->contains($ordoVaccination)) {
            $this->ordoVaccinations[] = $ordoVaccination;
            $ordoVaccination->setVaccin($this);
        }

        return $this;
    }

    public function removeOrdoVaccination(OrdoVaccination $ordoVaccination): self
    {
        if ($this->ordoVaccinations->contains($ordoVaccination)) {
            $this->ordoVaccinations->removeElement($ordoVaccination);
            // set the owning side to null (unless already changed)
            if ($ordoVaccination->getVaccin() === $this) {
                $ordoVaccination->setVaccin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CarnetVaccination[]
     */
    public function getCarnetVaccinations(): Collection
    {
        return $this->carnetVaccinations;
    }

    public function addCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if (!$this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations[] = $carnetVaccination;
            $carnetVaccination->setVaccin($this);
        }

        return $this;
    }

    public function removeCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if ($this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations->removeElement($carnetVaccination);
            // set the owning side to null (unless already changed)
            if ($carnetVaccination->getVaccin() === $this) {
                $carnetVaccination->setVaccin(null);
            }
        }

        return $this;
    }

    public function getDatePriseInitiale(): ?string
    {
        return $this->datePriseInitiale;
    }

    public function setDatePriseInitiale(?string $datePriseInitiale): self
    {
        $this->datePriseInitiale = $datePriseInitiale;

        return $this;
    }

    public function getRappel1(): ?string
    {
        return $this->rappel1;
    }

    public function setRappel1(?string $rappel1): self
    {
        $this->rappel1 = $rappel1;

        return $this;
    }

    public function getRappel2(): ?string
    {
        return $this->rappel2;
    }

    public function setRappel2(?string $rappel2): self
    {
        $this->rappel2 = $rappel2;

        return $this;
    }

    public function getRappel3(): ?string
    {
        return $this->rappel3;
    }

    public function setRappel3(?string $rappel3): self
    {
        $this->rappel3 = $rappel3;

        return $this;
    }

    public function getRappel4(): ?string
    {
        return $this->rappel4;
    }

    public function setRappel4(?string $rappel4): self
    {
        $this->rappel4 = $rappel4;

        return $this;
    }

    public function getRappel5(): ?string
    {
        return $this->rappel5;
    }

    public function setRappel5(?string $rappel5): self
    {
        $this->rappel5 = $rappel5;

        return $this;
    }

    public function getRappel6(): ?string
    {
        return $this->rappel6;
    }

    public function setRappel6(?string $rappel6): self
    {
        $this->rappel6 = $rappel6;

        return $this;
    }

    public function getRappel7(): ?string
    {
        return $this->rappel7;
    }

    public function setRappel7(?string $rappel7): self
    {
        $this->rappel7 = $rappel7;

        return $this;
    }

    public function getRappel8(): ?string
    {
        return $this->rappel8;
    }

    public function setRappel8(?string $rappel8): self
    {
        $this->rappel8 = $rappel8;

        return $this;
    }

    public function getRappel9(): ?string
    {
        return $this->rappel9;
    }

    public function setRappel9(?string $rappel9): self
    {
        $this->rappel9 = $rappel9;

        return $this;
    }

    public function getRappel10(): ?string
    {
        return $this->rappel10;
    }

    public function setRappel10(?string $rappel10): self
    {
        $this->rappel10 = $rappel10;

        return $this;
    }

    /**
     * @return Collection|PatientVaccin[]
     */
    public function getPatientVaccins(): Collection
    {
        return $this->patientVaccins;
    }

    public function addPatientVaccin(PatientVaccin $patientVaccin): self
    {
        if (!$this->patientVaccins->contains($patientVaccin)) {
            $this->patientVaccins[] = $patientVaccin;
            $patientVaccin->setVaccin($this);
        }

        return $this;
    }

    public function removePatientVaccin(PatientVaccin $patientVaccin): self
    {
        if ($this->patientVaccins->contains($patientVaccin)) {
            $this->patientVaccins->removeElement($patientVaccin);
            // set the owning side to null (unless already changed)
            if ($patientVaccin->getVaccin() === $this) {
                $patientVaccin->setVaccin(null);
            }
        }

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
            $vaccinCentreHealth->setVaccin($this);
        }

        return $this;
    }

    public function removeVaccinCentreHealth(VaccinCentreHealth $vaccinCentreHealth): self
    {
        if ($this->vaccinCentreHealths->contains($vaccinCentreHealth)) {
            $this->vaccinCentreHealths->removeElement($vaccinCentreHealth);
            // set the owning side to null (unless already changed)
            if ($vaccinCentreHealth->getVaccin() === $this) {
                $vaccinCentreHealth->setVaccin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VaccinPraticien[]
     */
    public function getVaccinPraticiens(): Collection
    {
        return $this->vaccinPraticiens;
    }

    public function addVaccinPraticien(VaccinPraticien $vaccinPraticien): self
    {
        if (!$this->vaccinPraticiens->contains($vaccinPraticien)) {
            $this->vaccinPraticiens[] = $vaccinPraticien;
            $vaccinPraticien->setVaccin($this);
        }

        return $this;
    }

    public function removeVaccinPraticien(VaccinPraticien $vaccinPraticien): self
    {
        if ($this->vaccinPraticiens->contains($vaccinPraticien)) {
            $this->vaccinPraticiens->removeElement($vaccinPraticien);
            // set the owning side to null (unless already changed)
            if ($vaccinPraticien->getVaccin() === $this) {
                $vaccinPraticien->setVaccin(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getVaccinName();
    }
}

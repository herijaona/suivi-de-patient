<?php

namespace App\Entity;

use App\Repository\OrdoVaccinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdoVaccinationRepository::class)
 */
class OrdoVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="ordoVaccinations")
     */
    private $vaccin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePrise;

    /**
     * @ORM\ManyToOne(targetEntity=Ordonnace::class, inversedBy="ordoVaccinations")
     */
    private $ordonnance;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="ordoVaccinations")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="ordoVaccinationPraticienExecutant")
     */
    private $referencePraticienExecutant;

    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="ordoVaccination")
     */
    private $interventionVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoVaccination::class, mappedBy="ordoVaccination")
     */
    private $patientOrdoVaccinations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statusVaccin;

    public function __construct()
    {
        $this->interventionVaccinations = new ArrayCollection();
        $this->patientOrdoVaccinations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDatePrise(): ?\DateTimeInterface
    {
        return $this->datePrise;
    }

    public function setDatePrise(\DateTimeInterface $datePrise): self
    {
        $this->datePrise = $datePrise;

        return $this;
    }

    public function getOrdonnance(): ?Ordonnace
    {
        return $this->ordonnance;
    }

    public function setOrdonnance(?Ordonnace $ordonnance): self
    {
        $this->ordonnance = $ordonnance;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getReferencePraticienExecutant(): ?Praticien
    {
        return $this->referencePraticienExecutant;
    }

    public function setReferencePraticienExecutant(?Praticien $referencePraticienExecutant): self
    {
        $this->referencePraticienExecutant = $referencePraticienExecutant;

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
            $interventionVaccination->setOrdoVaccination($this);
        }

        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getOrdoVaccination() === $this) {
                $interventionVaccination->setOrdoVaccination(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PatientOrdoVaccination[]
     */
    public function getPatientOrdoVaccinations(): Collection
    {
        return $this->patientOrdoVaccinations;
    }

    public function addPatientOrdoVaccination(PatientOrdoVaccination $patientOrdoVaccination): self
    {
        if (!$this->patientOrdoVaccinations->contains($patientOrdoVaccination)) {
            $this->patientOrdoVaccinations[] = $patientOrdoVaccination;
            $patientOrdoVaccination->setOrdoVaccination($this);
        }

        return $this;
    }

    public function removePatientOrdoVaccination(PatientOrdoVaccination $patientOrdoVaccination): self
    {
        if ($this->patientOrdoVaccinations->contains($patientOrdoVaccination)) {
            $this->patientOrdoVaccinations->removeElement($patientOrdoVaccination);
            // set the owning side to null (unless already changed)
            if ($patientOrdoVaccination->getOrdoVaccination() === $this) {
                $patientOrdoVaccination->setOrdoVaccination(null);
            }
        }

        return $this;
    }

    public function getStatusVaccin(): ?integer
    {
        return $this->statusVaccin;
    }

    public function setStatusVaccin(integer $statusVaccin): self
    {
        $this->statusVaccin = $statusVaccin;

        return $this;
    }
}

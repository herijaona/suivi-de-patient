<?php

namespace App\Entity;

use App\Repository\IntervationConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IntervationConsultationRepository::class)
 */
class IntervationConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoConsultation::class, inversedBy="intervationConsultations")
     */
    private $ordoConsulataion;

    /**
     * @ORM\ManyToOne(targetEntity=IntervationMedicale::class, inversedBy="intervationConsultations")
     */
    private $intervationMedicale;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateConsultation;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="intervationConsultations")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="intervationConsultationsPraticienPrescripteur")
     */
    private $praticienPrescripteur;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="intervationConsultationsPraticienConsultant")
     */
    private $praticienConsultant;

    /**
     * @ORM\OneToMany(targetEntity=PatientIntervationConsultation::class, mappedBy="interventionConsultation")
     */
    private $patientIntervationConsultations;

    public function __construct()
    {
        $this->patientIntervationConsultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdoConsulataion(): ?OrdoConsultation
    {
        return $this->ordoConsulataion;
    }

    public function setOrdoConsulataion(?OrdoConsultation $ordoConsulataion): self
    {
        $this->ordoConsulataion = $ordoConsulataion;

        return $this;
    }

    public function getIntervationMedicale(): ?IntervationMedicale
    {
        return $this->intervationMedicale;
    }

    public function setIntervationMedicale(?IntervationMedicale $intervationMedicale): self
    {
        $this->intervationMedicale = $intervationMedicale;

        return $this;
    }

    public function getDateConsultation(): ?\DateTimeInterface
    {
        return $this->dateConsultation;
    }

    public function setDateConsultation(?\DateTimeInterface $dateConsultation): self
    {
        $this->dateConsultation = $dateConsultation;

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

    public function getPraticienPrescripteur(): ?Praticien
    {
        return $this->praticienPrescripteur;
    }

    public function setPraticienPrescripteur(?Praticien $praticienPrescripteur): self
    {
        $this->praticienPrescripteur = $praticienPrescripteur;

        return $this;
    }

    public function getPraticienConsultant(): ?Praticien
    {
        return $this->praticienConsultant;
    }

    public function setPraticienConsultant(?Praticien $praticienConsultant): self
    {
        $this->praticienConsultant = $praticienConsultant;

        return $this;
    }

    /**
     * @return Collection|PatientIntervationConsultation[]
     */
    public function getPatientIntervationConsultations(): Collection
    {
        return $this->patientIntervationConsultations;
    }

    public function addPatientIntervationConsultation(PatientIntervationConsultation $patientIntervationConsultation): self
    {
        if (!$this->patientIntervationConsultations->contains($patientIntervationConsultation)) {
            $this->patientIntervationConsultations[] = $patientIntervationConsultation;
            $patientIntervationConsultation->setInterventionConsultation($this);
        }

        return $this;
    }

    public function removePatientIntervationConsultation(PatientIntervationConsultation $patientIntervationConsultation): self
    {
        if ($this->patientIntervationConsultations->contains($patientIntervationConsultation)) {
            $this->patientIntervationConsultations->removeElement($patientIntervationConsultation);
            // set the owning side to null (unless already changed)
            if ($patientIntervationConsultation->getInterventionConsultation() === $this) {
                $patientIntervationConsultation->setInterventionConsultation(null);
            }
        }

        return $this;
    }
}

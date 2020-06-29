<?php

namespace App\Entity;

use App\Repository\PatientIntervationConsultationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientIntervationConsultationRepository::class)
 */
class PatientIntervationConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientIntervationConsultations")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=IntervationConsultation::class, inversedBy="patientIntervationConsultations")
     */
    private $interventionConsultation;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInterventionConsultation(): ?IntervationConsultation
    {
        return $this->interventionConsultation;
    }

    public function setInterventionConsultation(?IntervationConsultation $interventionConsultation): self
    {
        $this->interventionConsultation = $interventionConsultation;

        return $this;
    }
}

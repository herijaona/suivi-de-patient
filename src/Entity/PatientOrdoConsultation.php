<?php

namespace App\Entity;

use App\Repository\PatientOrdoConsultationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientOrdoConsultationRepository::class)
 */
class PatientOrdoConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientOrdoConsultations")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoConsultation::class, inversedBy="patientOrdoConsultations")
     */
    private $ordoConsultation;

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

    public function getOrdoConsultation(): ?OrdoConsultation
    {
        return $this->ordoConsultation;
    }

    public function setOrdoConsultation(?OrdoConsultation $ordoConsultation): self
    {
        $this->ordoConsultation = $ordoConsultation;

        return $this;
    }
}

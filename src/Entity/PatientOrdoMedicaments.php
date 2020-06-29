<?php

namespace App\Entity;

use App\Repository\PatientOrdoMedicamentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientOrdoMedicamentsRepository::class)
 */
class PatientOrdoMedicaments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientOrdoMedicaments")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoMedicaments::class, inversedBy="patientOrdoMedicaments")
     */
    private $ordoMedicaments;

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

    public function getOrdoMedicaments(): ?OrdoMedicaments
    {
        return $this->ordoMedicaments;
    }

    public function setOrdoMedicaments(?OrdoMedicaments $ordoMedicaments): self
    {
        $this->ordoMedicaments = $ordoMedicaments;

        return $this;
    }
}

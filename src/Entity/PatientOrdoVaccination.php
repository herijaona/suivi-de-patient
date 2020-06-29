<?php

namespace App\Entity;

use App\Repository\PatientOrdoVaccinationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientOrdoVaccinationRepository::class)
 */
class PatientOrdoVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientOrdoVaccinations")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoVaccination::class, inversedBy="patientOrdoVaccinations")
     */
    private $ordoVaccination;

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

    public function getOrdoVaccination(): ?OrdoVaccination
    {
        return $this->ordoVaccination;
    }

    public function setOrdoVaccination(?OrdoVaccination $ordoVaccination): self
    {
        $this->ordoVaccination = $ordoVaccination;

        return $this;
    }
}

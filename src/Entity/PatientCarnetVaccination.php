<?php

namespace App\Entity;

use App\Repository\PatientCarnetVaccinationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientCarnetVaccinationRepository::class)
 */
class PatientCarnetVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientCarnetVaccinations")
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=CarnetVaccination::class, inversedBy="patientCarnetVaccinations")
     */
    private $carnetVaccination;

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

    public function getCarnetVaccination(): ?CarnetVaccination
    {
        return $this->carnetVaccination;
    }

    public function setCarnetVaccination(?CarnetVaccination $carnetVaccination): self
    {
        $this->carnetVaccination = $carnetVaccination;

        return $this;
    }
}

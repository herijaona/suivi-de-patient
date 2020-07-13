<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PatientCarnetVaccinationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientCarnetVaccinationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PatientCarnetVaccination"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PatientCarnetVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PatientCarnetVaccination"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientCarnetVaccinations")
     * @Groups({"read:PatientCarnetVaccination"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=CarnetVaccination::class, inversedBy="patientCarnetVaccinations")
     * @Groups({"read:PatientCarnetVaccination"})
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

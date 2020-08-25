<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PatientOrdoVaccinationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientOrdoVaccinationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PatientOrdoVaccination"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PatientOrdoVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PatientOrdoVaccination"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientOrdoVaccinations")
     * @Groups({"read:PatientOrdoVaccination"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoVaccination::class, inversedBy="patientOrdoVaccinations")
     * @Groups({"read:PatientOrdoVaccination"})
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

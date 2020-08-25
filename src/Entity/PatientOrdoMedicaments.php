<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PatientOrdoMedicamentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientOrdoMedicamentsRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PatientOrdoMedicaments"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PatientOrdoMedicaments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PatientOrdoMedicaments"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientOrdoMedicaments")
     * @Groups({"read:PatientOrdoMedicaments"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoMedicaments::class, inversedBy="patientOrdoMedicaments")
     * @Groups({"read:PatientOrdoMedicaments"})
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

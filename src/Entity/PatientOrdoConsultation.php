<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PatientOrdoConsultationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientOrdoConsultationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PatientOrdoConsultation"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PatientOrdoConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PatientOrdoConsultation"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientOrdoConsultations")
     * @Groups({"read:PatientOrdoConsultation"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoConsultation::class, inversedBy="patientOrdoConsultations")
     * @Groups({"read:PatientOrdoConsultation"})
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

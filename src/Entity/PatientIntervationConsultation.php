<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PatientIntervationConsultationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientIntervationConsultationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PatientIntervationConsultation"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PatientIntervationConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PatientIntervationConsultation"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientIntervationConsultations")
     * @Groups({"read:PatientIntervationConsultation"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=IntervationConsultation::class, inversedBy="patientIntervationConsultations")
     * @Groups({"read:PatientIntervationConsultation"})
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

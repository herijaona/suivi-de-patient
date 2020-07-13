<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PatientVaccinRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientVaccinRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PatientVaccin"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PatientVaccin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PatientVaccin"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientVaccins")
     * @Groups({"read:PatientVaccin"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="patientVaccins")
     * @Groups({"read:PatientVaccin"})
     */
    private $vaccin;

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

    public function getVaccin(): ?Vaccin
    {
        return $this->vaccin;
    }

    public function setVaccin(?Vaccin $vaccin): self
    {
        $this->vaccin = $vaccin;

        return $this;
    }
}

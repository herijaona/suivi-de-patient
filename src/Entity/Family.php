<?php

namespace App\Entity;

use App\Repository\FamilyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FamilyRepository::class)
 */
class Family
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="families")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient_parent;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="family_child")
     */
    private $patient_child;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatientParent(): ?Patient
    {
        return $this->patient_parent;
    }

    public function setPatientParent(?Patient $patient_parent): self
    {
        $this->patient_parent = $patient_parent;

        return $this;
    }

    public function getPatientChild(): ?Patient
    {
        return $this->patient_child;
    }

    public function setPatientChild(?Patient $patient_child): self
    {
        $this->patient_child = $patient_child;

        return $this;
    }
}

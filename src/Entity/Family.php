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
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="family_child")
     */
    private $patient_child;

    /**
     * @ORM\ManyToOne(targetEntity=GroupFamily::class, inversedBy="families")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_family;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGroupFamily(): ?GroupFamily
    {
        return $this->group_family;
    }

    public function setGroupFamily(?GroupFamily $group_family): self
    {
        $this->group_family = $group_family;

        return $this;
    }
}

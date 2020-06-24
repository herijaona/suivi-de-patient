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
    private $patientChild;

    /**
     * @ORM\ManyToOne(targetEntity=GroupFamily::class, inversedBy="families")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupFamily;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getPatientChild(): ?Patient
    {
        return $this->patientChild;
    }

    public function setPatientChild(?Patient $patientChild): self
    {
        $this->patientChild = $patientChild;

        return $this;
    }

    public function getGroupFamily(): ?GroupFamily
    {
        return $this->groupFamily;
    }

    public function setGroupFamily(?GroupFamily $groupFamily): self
    {
        $this->groupFamily = $groupFamily;

        return $this;
    }
}

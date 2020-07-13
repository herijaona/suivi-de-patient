<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\FamilyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FamilyRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:Family"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class Family
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:Family"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="familyChild")
     * @Groups({"read:Family"})
     */
    private $patientChild;

    /**
     * @ORM\ManyToOne(targetEntity=GroupFamily::class, inversedBy="families")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read:Family"})
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

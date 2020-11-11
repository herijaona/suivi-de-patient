<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
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
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "patientChild": "partial", "groupFamily": "partial"})
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

    /**
     * @ORM\Column(type="boolean")
     */
    private $Referent;

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

    public function getReferent(): ?bool
    {
        return $this->Referent;
    }

    public function setReferent(bool $Referent): self
    {
        $this->Referent = $Referent;

        return $this;
    }
}

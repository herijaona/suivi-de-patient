<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\GroupFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupFamilyRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:GroupFamily"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class GroupFamily
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:GroupFamily"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="groupFamily")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read:GroupFamily"})
     */
    private $patient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:GroupFamily"})
     */
    private $designation;

    /**
     * @ORM\OneToMany(targetEntity=Family::class, mappedBy="groupFamily")
     */
    private $families;

    public function __construct()
    {
        $this->families = new ArrayCollection();
    }

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

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return Collection|Family[]
     */
    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): self
    {
        if (!$this->families->contains($family)) {
            $this->families[] = $family;
            $family->setGroupFamily($this);
        }

        return $this;
    }

    public function removeFamily(Family $family): self
    {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            // set the owning side to null (unless already changed)
            if ($family->getGroupFamily() === $this) {
                $family->setGroupFamily(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\TypePatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypePatientRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:typepatient"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class TypePatient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:typepatient", "read:patient"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:typepatient"})
     */
    private $typePatientName;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="ManyToOne")
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="typePatient")
     */
    private $typePatient;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
        $this->typePatient = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypePatientName(): ?string
    {
        return $this->typePatientName;
    }

    public function setTypePatientName(string $type_patient_name): self
    {
        $this->typePatientName = $type_patient_name;

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setManyToOne($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getManyToOne() === $this) {
                $patient->setManyToOne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getTypePatient(): Collection
    {
        return $this->typePatient;
    }

    public function addTypePatient(Patient $typePatient): self
    {
        if (!$this->typePatient->contains($typePatient)) {
            $this->typePatient[] = $typePatient;
            $typePatient->setTypePatient($this);
        }

        return $this;
    }

    public function removeTypePatient(Patient $typePatient): self
    {
        if ($this->typePatient->contains($typePatient)) {
            $this->typePatient->removeElement($typePatient);
            // set the owning side to null (unless already changed)
            if ($typePatient->getTypePatient() === $this) {
                $typePatient->setTypePatient(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getTypePatientName();
    }
}

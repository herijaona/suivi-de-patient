<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\IntervationMedicaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IntervationMedicaleRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:IntervationMedicale"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class IntervationMedicale
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:IntervationMedicale"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:IntervationMedicale"})
     */
    private $typeIntervation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:IntervationMedicale"})
     */
    private $natureIntervation;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:IntervationMedicale"})
     */
    private $dateIntervation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:IntervationMedicale"})
     */
    private $lieuIntervation;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="intervationMedicales")
     * @Groups({"read:IntervationMedicale"})
     */
    private $praticien;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"read:IntervationMedicale"})
     */
    private $IdSantePatient;

    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="intervationMedicale")
     * @Groups({"read:IntervationMedicale"})
     */
    private $interventionVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=IntervationConsultation::class, mappedBy="intervationMedicale")
     * @Groups({"read:IntervationMedicale"})
     */
    private $intervationConsultations;

    public function __construct()
    {
        $this->interventionVaccinations = new ArrayCollection();
        $this->intervationConsultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeIntervation(): ?string
    {
        return $this->typeIntervation;
    }

    public function setTypeIntervation(string $typeIntervation): self
    {
        $this->typeIntervation = $typeIntervation;

        return $this;
    }

    public function getNatureIntervation(): ?string
    {
        return $this->natureIntervation;
    }

    public function setNatureIntervation(?string $natureIntervation): self
    {
        $this->natureIntervation = $natureIntervation;

        return $this;
    }

    public function getDateIntervation(): ?\DateTimeInterface
    {
        return $this->dateIntervation;
    }

    public function setDateIntervation(\DateTimeInterface $dateIntervation): self
    {
        $this->dateIntervation = $dateIntervation;

        return $this;
    }

    public function getLieuIntervation(): ?string
    {
        return $this->lieuIntervation;
    }

    public function setLieuIntervation(?string $lieuIntervation): self
    {
        $this->lieuIntervation = $lieuIntervation;

        return $this;
    }

    public function getPraticien(): ?Praticien
    {
        return $this->praticien;
    }

    public function setPraticien(?Praticien $praticien): self
    {
        $this->praticien = $praticien;

        return $this;
    }

    public function getIdSantePatient(): ?string
    {
        return $this->IdSantePatient;
    }

    public function setIdSantePatient(?string $IdSantePatient): self
    {
        $this->IdSantePatient = $IdSantePatient;

        return $this;
    }

    /**
     * @return Collection|InterventionVaccination[]
     */
    public function getInterventionVaccinations(): Collection
    {
        return $this->interventionVaccinations;
    }

    public function addInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if (!$this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations[] = $interventionVaccination;
            $interventionVaccination->setIntervationMedicale($this);
        }

        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getIntervationMedicale() === $this) {
                $interventionVaccination->setIntervationMedicale(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntervationConsultation[]
     */
    public function getIntervationConsultations(): Collection
    {
        return $this->intervationConsultations;
    }

    public function addIntervationConsultation(IntervationConsultation $intervationConsultation): self
    {
        if (!$this->intervationConsultations->contains($intervationConsultation)) {
            $this->intervationConsultations[] = $intervationConsultation;
            $intervationConsultation->setIntervationMedicale($this);
        }

        return $this;
    }

    public function removeIntervationConsultation(IntervationConsultation $intervationConsultation): self
    {
        if ($this->intervationConsultations->contains($intervationConsultation)) {
            $this->intervationConsultations->removeElement($intervationConsultation);
            // set the owning side to null (unless already changed)
            if ($intervationConsultation->getIntervationMedicale() === $this) {
                $intervationConsultation->setIntervationMedicale(null);
            }
        }

        return $this;
    }
}

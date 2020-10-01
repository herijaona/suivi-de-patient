<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\OrdonnaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdonnaceRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:Ordonnace"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class Ordonnace
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:Ordonnace","read:OrdoConsultation"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:Ordonnace"})
     */
    private $datePrescription;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="ordonnaces")
     * @Groups({"read:Ordonnace"})
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="ordonnacesMedecin")
     * @Groups({"read:Ordonnace"})
     */
    private $medecinTraitant;

    /**
     * @ORM\OneToMany(targetEntity=OrdoVaccination::class, mappedBy="ordonnance")
     * @Groups({"read:Ordonnace"})
     */
    private $ordoVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoConsultation::class, mappedBy="ordonnance")
     * @Groups({"read:Ordonnace"})
     */
    private $ordoConsultations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoMedicaments::class, mappedBy="ordonnance")
     * @Groups({"read:Ordonnace"})
     */
    private $ordoMedicaments;

    /**
     * @ORM\ManyToOne(targetEntity=CentreHealth::class, inversedBy="ordonnaces")
     */
    private $CentreSante;

    /**
     * @ORM\OneToMany(targetEntity=IntervationConsultation::class, mappedBy="ordonnace")
     */
    private $intervationConsultations;

    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="ordonnace")
     */
    private $interventionVaccinations;

    public function __construct()
    {
        $this->ordoVaccinations = new ArrayCollection();
        $this->ordoConsultations = new ArrayCollection();
        $this->ordoMedicaments = new ArrayCollection();
        $this->intervationConsultations = new ArrayCollection();
        $this->interventionVaccinations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePrescription(): ?\DateTimeInterface
    {
        return $this->datePrescription;
    }

    public function setDatePrescription(?\DateTimeInterface $datePrescription): self
    {
        $this->datePrescription = $datePrescription;

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

    public function getMedecinTraitant(): ?Praticien
    {
        return $this->medecinTraitant;
    }

    public function setMedecinTraitant(?Praticien $medecinTraitant): self
    {
        $this->medecinTraitant = $medecinTraitant;

        return $this;
    }

    /**
     * @return Collection|OrdoVaccination[]
     */
    public function getOrdoVaccinations(): Collection
    {
        return $this->ordoVaccinations;
    }

    public function addOrdoVaccination(OrdoVaccination $ordoVaccination): self
    {
        if (!$this->ordoVaccinations->contains($ordoVaccination)) {
            $this->ordoVaccinations[] = $ordoVaccination;
            $ordoVaccination->setOrdonnance($this);
        }

        return $this;
    }

    public function removeOrdoVaccination(OrdoVaccination $ordoVaccination): self
    {
        if ($this->ordoVaccinations->contains($ordoVaccination)) {
            $this->ordoVaccinations->removeElement($ordoVaccination);
            // set the owning side to null (unless already changed)
            if ($ordoVaccination->getOrdonnance() === $this) {
                $ordoVaccination->setOrdonnance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdoConsultation[]
     */
    public function getOrdoConsultations(): Collection
    {
        return $this->ordoConsultations;
    }

    public function addOrdoConsultation(OrdoConsultation $ordoConsultation): self
    {
        if (!$this->ordoConsultations->contains($ordoConsultation)) {
            $this->ordoConsultations[] = $ordoConsultation;
            $ordoConsultation->setOrdonnance($this);
        }

        return $this;
    }

    public function removeOrdoConsultation(OrdoConsultation $ordoConsultation): self
    {
        if ($this->ordoConsultations->contains($ordoConsultation)) {
            $this->ordoConsultations->removeElement($ordoConsultation);
            // set the owning side to null (unless already changed)
            if ($ordoConsultation->getOrdonnance() === $this) {
                $ordoConsultation->setOrdonnance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdoMedicaments[]
     */
    public function getOrdoMedicaments(): Collection
    {
        return $this->ordoMedicaments;
    }

    public function addOrdoMedicament(OrdoMedicaments $ordoMedicament): self
    {
        if (!$this->ordoMedicaments->contains($ordoMedicament)) {
            $this->ordoMedicaments[] = $ordoMedicament;
            $ordoMedicament->setOrdonnance($this);
        }

        return $this;
    }

    public function removeOrdoMedicament(OrdoMedicaments $ordoMedicament): self
    {
        if ($this->ordoMedicaments->contains($ordoMedicament)) {
            $this->ordoMedicaments->removeElement($ordoMedicament);
            // set the owning side to null (unless already changed)
            if ($ordoMedicament->getOrdonnance() === $this) {
                $ordoMedicament->setOrdonnance(null);
            }
        }

        return $this;
    }

    public function getCentreSante(): ?CentreHealth
    {
        return $this->CentreSante;
    }

    public function setCentreSante(?CentreHealth $CentreSante): self
    {
        $this->CentreSante = $CentreSante;

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
            $intervationConsultation->setOrdonnace($this);
        }

        return $this;
    }

    public function removeIntervationConsultation(IntervationConsultation $intervationConsultation): self
    {
        if ($this->intervationConsultations->contains($intervationConsultation)) {
            $this->intervationConsultations->removeElement($intervationConsultation);
            // set the owning side to null (unless already changed)
            if ($intervationConsultation->getOrdonnace() === $this) {
                $intervationConsultation->setOrdonnace(null);
            }
        }

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
            $interventionVaccination->setOrdonnace($this);
        }

        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getOrdonnace() === $this) {
                $interventionVaccination->setOrdonnace(null);
            }
        }

        return $this;
    }
}

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

    public function __construct()
    {
        $this->ordoVaccinations = new ArrayCollection();
        $this->ordoConsultations = new ArrayCollection();
        $this->ordoMedicaments = new ArrayCollection();
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
}

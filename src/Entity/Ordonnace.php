<?php

namespace App\Entity;

use App\Repository\OrdonnaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdonnaceRepository::class)
 */
class Ordonnace
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datePrescription;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="ordonnaces")
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="ordonnacesMedecin")
     */
    private $medecinTraitant;

    /**
     * @ORM\OneToMany(targetEntity=OrdoVaccination::class, mappedBy="ordonnance")
     */
    private $ordoVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoConsultation::class, mappedBy="ordonnance")
     */
    private $ordoConsultations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoMedicaments::class, mappedBy="ordonnance")
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

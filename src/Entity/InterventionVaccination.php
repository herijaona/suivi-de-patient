<?php

namespace App\Entity;

use App\Repository\InterventionVaccinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterventionVaccinationRepository::class)
 */
class InterventionVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="interventionVaccinations")
     */
    private $vaccin;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $statusVaccin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePriseVaccin;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="interventionVaccinations")
     */
    private $praticienPrescripteur;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="interventionExecutant")
     */
    private $praticienExecutant;

    /**
     * @ORM\ManyToOne(targetEntity=IntervationMedicale::class, inversedBy="interventionVaccinations")
     */
    private $intervationMedicale;

    /**
     * @ORM\ManyToOne(targetEntity=OrdoVaccination::class, inversedBy="interventionVaccinations")
     */
    private $ordoVaccination;

    /**
     * @ORM\OneToMany(targetEntity=CarnetVaccination::class, mappedBy="intervationVaccination")
     */
    private $carnetVaccinations;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="interventionVaccinations")
     */
    private $patient;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etat;

    public function __construct()
    {
        $this->carnetVaccinations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVaccin(): ?Vaccin
    {
        return $this->vaccin;
    }

    public function setVaccin(?Vaccin $vaccin): self
    {
        $this->vaccin = $vaccin;

        return $this;
    }

    public function getStatusVaccin(): ?string
    {
        return $this->statusVaccin;
    }

    public function setStatusVaccin(?string $statusVaccin): self
    {
        $this->statusVaccin = $statusVaccin;

        return $this;
    }

    public function getDatePriseVaccin(): ?\DateTimeInterface
    {
        return $this->datePriseVaccin;
    }

    public function setDatePriseVaccin(\DateTimeInterface $datePriseVaccin): self
    {
        $this->datePriseVaccin = $datePriseVaccin;

        return $this;
    }

    public function getPraticienPrescripteur(): ?Praticien
    {
        return $this->praticienPrescripteur;
    }

    public function setPraticienPrescripteur(?Praticien $praticienPrescripteur): self
    {
        $this->praticienPrescripteur = $praticienPrescripteur;

        return $this;
    }

    public function getPraticienExecutant(): ?Praticien
    {
        return $this->praticienExecutant;
    }

    public function setPraticienExecutant(?Praticien $praticienExecutant): self
    {
        $this->praticienExecutant = $praticienExecutant;

        return $this;
    }

    public function getIntervationMedicale(): ?IntervationMedicale
    {
        return $this->intervationMedicale;
    }

    public function setIntervationMedicale(?IntervationMedicale $intervationMedicale): self
    {
        $this->intervationMedicale = $intervationMedicale;

        return $this;
    }

    public function getOrdoVaccination(): ?OrdoVaccination
    {
        return $this->ordoVaccination;
    }

    public function setOrdoVaccination(?OrdoVaccination $ordoVaccination): self
    {
        $this->ordoVaccination = $ordoVaccination;

        return $this;
    }

    /**
     * @return Collection|CarnetVaccination[]
     */
    public function getCarnetVaccinations(): Collection
    {
        return $this->carnetVaccinations;
    }

    public function addCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if (!$this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations[] = $carnetVaccination;
            $carnetVaccination->setIntervationVaccination($this);
        }

        return $this;
    }

    public function removeCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if ($this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations->removeElement($carnetVaccination);
            // set the owning side to null (unless already changed)
            if ($carnetVaccination->getIntervationVaccination() === $this) {
                $carnetVaccination->setIntervationVaccination(null);
            }
        }

        return $this;
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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}

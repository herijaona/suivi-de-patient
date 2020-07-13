<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\InterventionVaccinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterventionVaccinationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:InterventionVaccination"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class InterventionVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:InterventionVaccination"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="interventionVaccinations")
     * @Groups({"read:InterventionVaccination"})
     */
    private $vaccin;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"read:InterventionVaccination"})
     */
    private $statusVaccin;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:InterventionVaccination"})
     */
    private $datePriseVaccin;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="interventionVaccinations")
     * @Groups({"read:InterventionVaccination"})
     */
    private $praticienPrescripteur;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="interventionExecutant")
     * @Groups({"read:InterventionVaccination"})
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
     * @Groups({"read:InterventionVaccination"})
     */
    private $patient;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:InterventionVaccination"})
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

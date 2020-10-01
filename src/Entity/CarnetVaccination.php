<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\CarnetVaccinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarnetVaccinationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:carnetvaccination"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class CarnetVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:carnetvaccination"})
     * @ApiFilter(SearchFilter::class, properties={"patient": "exact"})
     * @ApiFilter(DateFilter::class, properties={"datePriseInitiale": DateFilter::PARAMETER_AFTER})
     * @ApiFilter(OrderFilter::class, properties={"datePriseInitiale"}, arguments={"orderParameterName"="order"}))
     */
    private $id;



    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="carnetVaccinations")
     * @Groups({"read:carnetvaccination"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="carnetVaccinations")
     * @Groups({"read:carnetvaccination"})
     */
    private $vaccin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:carnetvaccination"})
     */
    private $etat;



    /**
     * @ORM\OneToMany(targetEntity=PatientCarnetVaccination::class, mappedBy="carnetVaccination")
     */
    private $patientCarnetVaccinations;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_prise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identification;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="carnetVaccinations")
     */
    private $Praticien;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identifiant_vaccin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Lot;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->patientCarnetVaccinations = new ArrayCollection();
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

    public function getVaccin(): ?Vaccin
    {
        return $this->vaccin;
    }

    public function setVaccin(?Vaccin $vaccin): self
    {
        $this->vaccin = $vaccin;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }



    /**
     * @return Collection|PatientCarnetVaccination[]
     */
    public function getPatientCarnetVaccinations(): Collection
    {
        return $this->patientCarnetVaccinations;
    }

    public function addPatientCarnetVaccination(PatientCarnetVaccination $patientCarnetVaccination): self
    {
        if (!$this->patientCarnetVaccinations->contains($patientCarnetVaccination)) {
            $this->patientCarnetVaccinations[] = $patientCarnetVaccination;
            $patientCarnetVaccination->setCarnetVaccination($this);
        }

        return $this;
    }

    public function removePatientCarnetVaccination(PatientCarnetVaccination $patientCarnetVaccination): self
    {
        if ($this->patientCarnetVaccinations->contains($patientCarnetVaccination)) {
            $this->patientCarnetVaccinations->removeElement($patientCarnetVaccination);
            // set the owning side to null (unless already changed)
            if ($patientCarnetVaccination->getCarnetVaccination() === $this) {
                $patientCarnetVaccination->setCarnetVaccination(null);
            }
        }

        return $this;
    }

    public function getDatePrise(): ?\DateTimeInterface
    {
        return $this->date_prise;
    }

    public function setDatePrise(?\DateTimeInterface $date_prise): self
    {
        $this->date_prise = $date_prise;

        return $this;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(?string $identification): self
    {
        $this->identification = $identification;

        return $this;
    }

    public function getPraticien(): ?Praticien
    {
        return $this->Praticien;
    }

    public function setPraticien(?Praticien $Praticien): self
    {
        $this->Praticien = $Praticien;

        return $this;
    }

    public function getIdentifiantVaccin(): ?string
    {
        return $this->identifiant_vaccin;
    }

    public function setIdentifiantVaccin(string $identifiant_vaccin): self
    {
        $this->identifiant_vaccin = $identifiant_vaccin;

        return $this;
    }

    public function getLot(): ?string
    {
        return $this->Lot;
    }

    public function setLot(?string $Lot): self
    {
        $this->Lot = $Lot;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }
}

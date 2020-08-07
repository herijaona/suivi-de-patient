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
     * @ORM\ManyToOne(targetEntity=InterventionVaccination::class, inversedBy="carnetVaccinations")
     * @Groups({"read:carnetvaccination"})
     */
    private $intervationVaccination;

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
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:carnetvaccination"})
     */
    private $datePriseInitiale;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:carnetvaccination"})
     */
    private $rappelVaccin;

    /**
     * @ORM\OneToMany(targetEntity=PatientCarnetVaccination::class, mappedBy="carnetVaccination")
     */
    private $patientCarnetVaccinations;

    public function __construct()
    {
        $this->patientCarnetVaccinations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntervationVaccination(): ?InterventionVaccination
    {
        return $this->intervationVaccination;
    }

    public function setIntervationVaccination(?InterventionVaccination $intervationVaccination): self
    {
        $this->intervationVaccination = $intervationVaccination;

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

    public function getDatePriseInitiale(): ?\DateTimeInterface
    {
        return $this->datePriseInitiale;
    }

    public function setDatePriseInitiale(?\DateTimeInterface $datePriseInitiale): self
    {
        $this->datePriseInitiale = $datePriseInitiale;

        return $this;
    }

    public function getRappelVaccin(): ?\DateTimeInterface
    {
        return $this->rappelVaccin;
    }

    public function setRappelVaccin(?\DateTimeInterface $rappelVaccin): self
    {
        $this->rappelVaccin = $rappelVaccin;

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
}

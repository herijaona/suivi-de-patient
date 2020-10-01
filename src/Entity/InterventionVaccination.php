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
     * @ORM\ManyToOne(targetEntity=IntervationMedicale::class, inversedBy="interventionVaccinations")
     */
    private $intervationMedicale;
    
    /**

     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="patientIntervationConsultations")

     */
    private $patient;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:InterventionVaccination"})
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Ordonnace::class, inversedBy="interventionVaccinations")
     */
    private $ordonnace;

    /**
     * @ORM\ManyToOne(targetEntity=CarnetVaccination::class, inversedBy="interventionVaccinations")
     */
    private $carnet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identification_vaccin;

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


    public function getIntervationMedicale(): ?IntervationMedicale
    {
        return $this->intervationMedicale;
    }

    public function setIntervationMedicale(?IntervationMedicale $intervationMedicale): self
    {
        $this->intervationMedicale = $intervationMedicale;

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

    public function getOrdonnace(): ?Ordonnace
    {
        return $this->ordonnace;
    }

    public function setOrdonnace(?Ordonnace $ordonnace): self
    {
        $this->ordonnace = $ordonnace;

        return $this;
    }

    public function getCarnet(): ?CarnetVaccination
    {
        return $this->carnet;
    }

    public function setCarnet(?CarnetVaccination $carnet): self
    {
        $this->carnet = $carnet;

        return $this;
    }

    public function getIdentificationVaccin(): ?string
    {
        return $this->identification_vaccin;
    }

    public function setIdentificationVaccin(?string $identification_vaccin): self
    {
        $this->identification_vaccin = $identification_vaccin;

        return $this;
    }


}

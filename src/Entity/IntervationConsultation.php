<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\IntervationConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IntervationConsultationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:IntervationConsultation"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"patient": "exact", "etat": "exact"})
 * @ApiFilter(DateFilter::class, properties={"dateConsultation": DateFilter::PARAMETER_AFTER})
 * @ApiFilter(OrderFilter::class, properties={"dateConsultation"}, arguments={"orderParameterName"="order"}))
 */
class IntervationConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:IntervationConsultation"})
     */
    private $id;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:IntervationConsultation"})
     */
    private $dateConsultation;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="intervationConsultations")
     * @Groups({"read:IntervationConsultation"})
     */
    private $patient;


    /**
     * @ORM\OneToMany(targetEntity=PatientIntervationConsultation::class, mappedBy="interventionConsultation")
     */
    private $patientIntervationConsultations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:IntervationConsultation"})
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Ordonnace::class, inversedBy="intervationConsultations")
     */
    private $ordonnace;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objetConsultation;


    public function __construct()
    {
        $this->patientIntervationConsultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return OrdoConsultation|null
     */


    public function getDateConsultation(): ?\DateTime
    {
        return $this->dateConsultation;
    }

    public function setDateConsultation(?\DateTime $dateConsultation): self
    {
        $this->dateConsultation = $dateConsultation;

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


    /**
     * @return Collection|PatientIntervationConsultation[]
     */
    public function getPatientIntervationConsultations(): Collection
    {
        return $this->patientIntervationConsultations;
    }

    public function addPatientIntervationConsultation(PatientIntervationConsultation $patientIntervationConsultation): self
    {
        if (!$this->patientIntervationConsultations->contains($patientIntervationConsultation)) {
            $this->patientIntervationConsultations[] = $patientIntervationConsultation;
            $patientIntervationConsultation->setInterventionConsultation($this);
        }

        return $this;
    }

    public function removePatientIntervationConsultation(PatientIntervationConsultation $patientIntervationConsultation): self
    {
        if ($this->patientIntervationConsultations->contains($patientIntervationConsultation)) {
            $this->patientIntervationConsultations->removeElement($patientIntervationConsultation);
            // set the owning side to null (unless already changed)
            if ($patientIntervationConsultation->getInterventionConsultation() === $this) {
                $patientIntervationConsultation->setInterventionConsultation(null);
            }
        }

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



    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getObjetConsultation(): ?string
    {
        return $this->objetConsultation;
    }

    public function setObjetConsultation(?string $objetConsultation): self
    {
        $this->objetConsultation = $objetConsultation;

        return $this;
    }
}

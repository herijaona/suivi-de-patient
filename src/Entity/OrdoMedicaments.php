<?php

namespace App\Entity;

use App\Repository\OrdoMedicamentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdoMedicamentsRepository::class)
 */
class OrdoMedicaments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Ordonnace::class, inversedBy="ordoMedicaments")
     */
    private $ordonnance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomMedicament;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $posologie;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $statutMedicament;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="ordoMedicaments")
     */
    private $patient;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $referencePraticienExecutant;

    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoMedicaments::class, mappedBy="ordoMedicaments")
     */
    private $patientOrdoMedicaments;

    public function __construct()
    {
        $this->patientOrdoMedicaments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdonnance(): ?Ordonnace
    {
        return $this->ordonnance;
    }

    public function setOrdonnance(?Ordonnace $ordonnance): self
    {
        $this->ordonnance = $ordonnance;

        return $this;
    }

    public function getNomMedicament(): ?string
    {
        return $this->nomMedicament;
    }

    public function setNomMedicament(string $nomMedicament): self
    {
        $this->nomMedicament = $nomMedicament;

        return $this;
    }

    public function getPosologie(): ?string
    {
        return $this->posologie;
    }

    public function setPosologie(string $posologie): self
    {
        $this->posologie = $posologie;

        return $this;
    }

    public function getStatutMedicament(): ?string
    {
        return $this->statutMedicament;
    }

    public function setStatutMedicament(string $statutMedicament): self
    {
        $this->statutMedicament = $statutMedicament;

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

    public function getReferencePraticienExecutant(): ?string
    {
        return $this->referencePraticienExecutant;
    }

    public function setReferencePraticienExecutant(string $referencePraticienExecutant): self
    {
        $this->referencePraticienExecutant = $referencePraticienExecutant;

        return $this;
    }

    /**
     * @return Collection|PatientOrdoMedicaments[]
     */
    public function getPatientOrdoMedicaments(): Collection
    {
        return $this->patientOrdoMedicaments;
    }

    public function addPatientOrdoMedicament(PatientOrdoMedicaments $patientOrdoMedicament): self
    {
        if (!$this->patientOrdoMedicaments->contains($patientOrdoMedicament)) {
            $this->patientOrdoMedicaments[] = $patientOrdoMedicament;
            $patientOrdoMedicament->setOrdoMedicaments($this);
        }

        return $this;
    }

    public function removePatientOrdoMedicament(PatientOrdoMedicaments $patientOrdoMedicament): self
    {
        if ($this->patientOrdoMedicaments->contains($patientOrdoMedicament)) {
            $this->patientOrdoMedicaments->removeElement($patientOrdoMedicament);
            // set the owning side to null (unless already changed)
            if ($patientOrdoMedicament->getOrdoMedicaments() === $this) {
                $patientOrdoMedicament->setOrdoMedicaments(null);
            }
        }

        return $this;
    }
}

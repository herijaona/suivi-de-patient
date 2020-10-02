<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\OrdoConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdoConsultationRepository::class)
 * @ApiResource(
 *    collectionOperations={
 *          "get"={
 *              "controller"=App\Controller\Api\Consultation\RetriveConsultation::class
 *          },
 *          "post"
 *     },
 *    itemOperations={
 *     "put",
 *     "delete"
 *     },
 *     normalizationContext={"groups"={"read:OrdoConsultation"}},
 *     denormalizationContext={"groups"={"write:OrdoConsultation"}}
 * )
 */
class OrdoConsultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:OrdoConsultation"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Ordonnace::class, inversedBy="ordoConsultations")
     * @Groups({"read:OrdoConsultation"})
     */
    private $ordonnance;

    /**
     * @ORM\Column(type="datetime", nullable= true)
     * @Groups({"read:OrdoConsultation"})
     */
    private $dateRdv;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="ordoConsultations")
     * @Groups({"read:OrdoConsultation"})
     */
    private $patient;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:OrdoConsultation", "read:IntervationConsultation"})
     */
    private $objetConsultation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:OrdoConsultation"})
     */
    private $statusConsultation;


    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoConsultation::class, mappedBy="ordoConsultation")
     * @Groups({"read:OrdoConsultation"})
     */
    private $patientOrdoConsultations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:OrdoConsultation"})
     */
    private $etat;



    public function __construct()
    {
        $this->patientOrdoConsultations = new ArrayCollection();
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

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->dateRdv;
    }

    public function setDateRdv(\DateTimeInterface $dateRdv): self
    {
        $this->dateRdv = $dateRdv;

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

    public function getObjetConsultation(): ?string
    {
        return $this->objetConsultation;
    }

    public function setObjetConsultation(string $objetConsultation): self
    {
        $this->objetConsultation = $objetConsultation;

        return $this;
    }

    public function getStatusConsultation(): ?int
    {
        return $this->statusConsultation;
    }

    public function setStatusConsultation(int $statusConsultation): self
    {
        $this->statusConsultation = $statusConsultation;

        return $this;
    }



    /**
     * @return Collection|PatientOrdoConsultation[]
     */
    public function getPatientOrdoConsultations(): Collection
    {
        return $this->patientOrdoConsultations;
    }

    public function addPatientOrdoConsultation(PatientOrdoConsultation $patientOrdoConsultation): self
    {
        if (!$this->patientOrdoConsultations->contains($patientOrdoConsultation)) {
            $this->patientOrdoConsultations[] = $patientOrdoConsultation;
            $patientOrdoConsultation->setOrdoConsultation($this);
        }

        return $this;
    }

    public function removePatientOrdoConsultation(PatientOrdoConsultation $patientOrdoConsultation): self
    {
        if ($this->patientOrdoConsultations->contains($patientOrdoConsultation)) {
            $this->patientOrdoConsultations->removeElement($patientOrdoConsultation);
            // set the owning side to null (unless already changed)
            if ($patientOrdoConsultation->getOrdoConsultation() === $this) {
                $patientOrdoConsultation->setOrdoConsultation(null);
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




}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\OrdoVaccinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "controller"=App\Controller\Api\Vaccination\RetriveVaccination::class
 *          },
 *          "post"
 *      },
 *     itemOperations={
 *        "get",
 *        "put",
 *        "delete"
 *     },
 *     attributes={
 *        "order"={"datePrise":"DESC"}
 *     },
 *     normalizationContext={"groups"={"ordovaccination:read"}},
 *     denormalizationContext={"groups"={"ordovaccination:write"}}
 * )
 * @ORM\Entity(repositoryClass=OrdoVaccinationRepository::class)
 */
class OrdoVaccination
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"ordovaccination:read"})
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity=Ordonnace::class, inversedBy="ordoVaccinations")
     * @Groups({"ordovaccination:read"})
     */
    private $ordonnance;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="ordoVaccinations")
     * @Groups({"ordovaccination:read"})
     */
    private $patient;



    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoVaccination::class, mappedBy="ordoVaccination")
     * @Groups({"ordovaccination:read"})
     */
    private $patientOrdoVaccinations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"ordovaccination:read"})
     */
    private $statusVaccin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"ordovaccination:read"})
     */
    private $etat;


    public function __construct()
    {

        $this->patientOrdoVaccinations = new ArrayCollection();
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
     * @return Collection|PatientOrdoVaccination[]
     */
    public function getPatientOrdoVaccinations(): Collection
    {
        return $this->patientOrdoVaccinations;
    }

    public function addPatientOrdoVaccination(PatientOrdoVaccination $patientOrdoVaccination): self
    {
        if (!$this->patientOrdoVaccinations->contains($patientOrdoVaccination)) {
            $this->patientOrdoVaccinations[] = $patientOrdoVaccination;
            $patientOrdoVaccination->setOrdoVaccination($this);
        }

        return $this;
    }

    public function removePatientOrdoVaccination(PatientOrdoVaccination $patientOrdoVaccination): self
    {
        if ($this->patientOrdoVaccinations->contains($patientOrdoVaccination)) {
            $this->patientOrdoVaccinations->removeElement($patientOrdoVaccination);
            // set the owning side to null (unless already changed)
            if ($patientOrdoVaccination->getOrdoVaccination() === $this) {
                $patientOrdoVaccination->setOrdoVaccination(null);
            }
        }

        return $this;
    }

    public function getStatusVaccin(): ?int
    {
        return $this->statusVaccin;
    }

    public function setStatusVaccin(int $statusVaccin): self
    {
        $this->statusVaccin = $statusVaccin;

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

    public function getStatusNotif(): ?int
    {
        return $this->statusNotif;
    }

    public function setStatusNotif(int $statusNotif): self
    {
        $this->statusNotif = $statusNotif;

        return $this;
    }
}

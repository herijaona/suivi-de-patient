<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PropositionRdvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropositionRdvRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PropositionRdv"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"praticien": "exact"})
 * @ApiFilter(DateFilter::class, properties={"dateConsultation": DateFilter::PARAMETER_AFTER})
 * @ApiFilter(OrderFilter::class, properties={"dateProposition"}, arguments={"orderParameterName"="order"}))
 */

class PropositionRdv
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PropositionRdv"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:PropositionRdv"})
     */
    private $dateProposition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:PropositionRdv"})
     */
    private $descriptionProposition;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:PropositionRdv"})
     */
    private $statusProposition;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="propositionRdvs")
     * @Groups({"read:PropositionRdv"})
     */
    private $praticien;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:PropositionRdv"})
     */
    private $etat;


    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="propositionRdvs")
     * @Groups({"read:PropositionRdv"})
     */
    private $patient;

    /**
     * @ORM\OneToOne(targetEntity=IntervationConsultation::class, mappedBy="proposition", cascade={"persist", "remove"})
     * @Groups({"read:PropositionRdv"})
     */
    private $intervationConsultation;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:PropositionRdv"})
     */
    private $statusNotif;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="propositionRdvs")
     */
    private $vaccin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    public function __construct()
    {
        $this->ordoConsultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateProposition(): ?\DateTimeInterface
    {
        return $this->dateProposition;
    }

    public function setDateProposition(\DateTimeInterface $dateProposition): self
    {
        $this->dateProposition = $dateProposition;

        return $this;
    }

    public function getDescriptionProposition(): ?string
    {
        return $this->descriptionProposition;
    }

    public function setDescriptionProposition(string $descriptionProposition): self
    {
        $this->descriptionProposition = $descriptionProposition;

        return $this;
    }

    public function getStatusProposition(): ?int
    {
        return $this->statusProposition;
    }

    public function setStatusProposition(?int $statusProposition): self
    {
        $this->statusProposition = $statusProposition;

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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

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
            $ordoConsultation->setProposition($this);
        }

        return $this;
    }

    public function removeOrdoConsultation(OrdoConsultation $ordoConsultation): self
    {
        if ($this->ordoConsultations->contains($ordoConsultation)) {
            $this->ordoConsultations->removeElement($ordoConsultation);
            // set the owning side to null (unless already changed)
            if ($ordoConsultation->getProposition() === $this) {
                $ordoConsultation->setProposition(null);
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

    public function getIntervationConsultation(): ?IntervationConsultation
    {
        return $this->intervationConsultation;
    }

    public function setIntervationConsultation(?IntervationConsultation $intervationConsultation): self
    {
        $this->intervationConsultation = $intervationConsultation;

        // set (or unset) the owning side of the relation if necessary
        $newProposition = null === $intervationConsultation ? null : $this;
        if ($intervationConsultation->getProposition() !== $newProposition) {
            $intervationConsultation->setProposition($newProposition);
        }

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

    public function getVaccin(): ?Vaccin
    {
        return $this->vaccin;
    }

    public function setVaccin(?Vaccin $vaccin): self
    {
        $this->vaccin = $vaccin;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}

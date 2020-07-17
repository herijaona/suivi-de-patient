<?php

namespace App\Entity;

use App\Repository\PropositionRdvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropositionRdvRepository::class)
 */
class PropositionRdv
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateProposition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionProposition;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statusProposition;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="propositionRdvs")
     */
    private $praticien;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="integer")
     */
    private $PersonneAttendre;

    /**
     * @ORM\OneToMany(targetEntity=OrdoConsultation::class, mappedBy="proposition")
     */
    private $ordoConsultations;

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

    public function getPersonneAttendre(): ?int
    {
        return $this->PersonneAttendre;
    }

    public function setPersonneAttendre(int $PersonneAttendre): self
    {
        $this->PersonneAttendre = $PersonneAttendre;

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
}

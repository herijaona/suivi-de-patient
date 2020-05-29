<?php

namespace App\Entity;

use App\Repository\VaccinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VaccinRepository::class)
 */
class Vaccin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $vaccin_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vaccin_description;

    /**
     * @ORM\ManyToOne(targetEntity=TypeVaccin::class, inversedBy="vaccins")
     */
    private $Type_vaccin;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="vaccins")
     */
    private $state;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity=RendezVous::class, mappedBy="vaccin")
     */
    private $rendezVous;

    function __construct()
    {
        $this->etat = true;
        $this->rendezVous = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVaccinName(): ?string
    {
        return $this->vaccin_name;
    }

    public function setVaccinName(string $vaccin_name): self
    {
        $this->vaccin_name = $vaccin_name;

        return $this;
    }

    public function getVaccinDescription(): ?string
    {
        return $this->vaccin_description;
    }

    public function setVaccinDescription(?string $vaccin_description): self
    {
        $this->vaccin_description = $vaccin_description;

        return $this;
    }

    public function getTypeVaccin(): ?TypeVaccin
    {
        return $this->Type_vaccin;
    }

    public function setTypeVaccin(?TypeVaccin $Type_vaccin): self
    {
        $this->Type_vaccin = $Type_vaccin;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|RendezVous[]
     */
    public function getRendezVous(): Collection
    {
        return $this->rendezVous;
    }

    public function addRendezVou(RendezVous $rendezVou): self
    {
        if (!$this->rendezVous->contains($rendezVou)) {
            $this->rendezVous[] = $rendezVou;
            $rendezVou->setVaccin($this);
        }

        return $this;
    }

    public function removeRendezVou(RendezVous $rendezVou): self
    {
        if ($this->rendezVous->contains($rendezVou)) {
            $this->rendezVous->removeElement($rendezVou);
            // set the owning side to null (unless already changed)
            if ($rendezVou->getVaccin() === $this) {
                $rendezVou->setVaccin(null);
            }
        }

        return $this;
    }
}

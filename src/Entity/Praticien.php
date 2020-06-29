<?php

namespace App\Entity;

use App\Repository\PraticienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PraticienRepository::class)
 */
class Praticien
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
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneProfessional;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonction;

    /**
     * @ORM\Column(type="date")
     */
    private $dateBorn;


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity=Ordonnace::class, mappedBy="praticien")
     */
    private $ordonnaces;

    /**
     * @ORM\OneToMany(targetEntity=Ordonnace::class, mappedBy="medecinTraitant")
     */
    private $ordonnacesMedecin;

    /**
     * @ORM\OneToMany(targetEntity=IntervationMedicale::class, mappedBy="praticien")
     */
    private $intervationMedicales;

    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="praticienPrescripteur")
     */
    private $interventionVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="praticienExecutant")
     */
    private $interventionExecutant;

    /**
     * @ORM\OneToMany(targetEntity=OrdoVaccination::class, mappedBy="referencePraticienExecutant")
     */
    private $ordoVaccinationPraticienExecutant;

    /**
     * @ORM\OneToMany(targetEntity=IntervationConsultation::class, mappedBy="praticienPrescripteur")
     */
    private $intervationConsultationsPraticienPrescripteur;

    /**
     * @ORM\OneToMany(targetEntity=IntervationConsultation::class, mappedBy="praticienConsultant")
     */
    private $intervationConsultationsPraticienConsultant;

    /**
     * @ORM\OneToMany(targetEntity=VaccinPraticien::class, mappedBy="praticien")
     */
    private $vaccinPraticiens;

    /**
     * @ORM\OneToMany(targetEntity=PraticienSpecialite::class, mappedBy="praticien")
     */
    private $praticienSpecialites;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="praticien")
     */
    private $address;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        $this->setCreatedAt(new \DateTime('now'));
        $this->ordonnaces = new ArrayCollection();
        $this->ordonnacesMedecin = new ArrayCollection();
        $this->intervationMedicales = new ArrayCollection();
        $this->interventionVaccinations = new ArrayCollection();
        $this->interventionExecutant = new ArrayCollection();
        $this->ordoVaccinationPraticienExecutant = new ArrayCollection();
        $this->intervationConsultationsPraticienPrescripteur = new ArrayCollection();
        $this->intervationConsultationsPraticienConsultant = new ArrayCollection();
        $this->vaccinPraticiens = new ArrayCollection();
        $this->praticienSpecialites = new ArrayCollection();
        $this->etat = false;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $first_name): self
    {
        $this->firstName = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $last_name): self
    {
        $this->lastName = $last_name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhoneProfessional(): ?string
    {
        return $this->phoneProfessional;
    }

    public function setPhoneProfessional(?string $phone_professional): self
    {
        $this->phoneProfessional = $phone_professional;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getDateBorn(): ?\DateTimeInterface
    {
        return $this->dateBorn;
    }

    public function setDateBorn(\DateTimeInterface $date_born): self
    {
        $this->dateBorn = $date_born;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->createdAt = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Ordonnace[]
     */
    public function getOrdonnaces(): Collection
    {
        return $this->ordonnaces;
    }

    public function addOrdonnace(Ordonnace $ordonnace): self
    {
        if (!$this->ordonnaces->contains($ordonnace)) {
            $this->ordonnaces[] = $ordonnace;
            $ordonnace->setPraticien($this);
        }

        return $this;
    }

    public function removeOrdonnace(Ordonnace $ordonnace): self
    {
        if ($this->ordonnaces->contains($ordonnace)) {
            $this->ordonnaces->removeElement($ordonnace);
            // set the owning side to null (unless already changed)
            if ($ordonnace->getPraticien() === $this) {
                $ordonnace->setPraticien(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ordonnace[]
     */
    public function getOrdonnacesMedecin(): Collection
    {
        return $this->ordonnacesMedecin;
    }

    public function addOrdonnacesMedecin(Ordonnace $ordonnacesMedecin): self
    {
        if (!$this->ordonnacesMedecin->contains($ordonnacesMedecin)) {
            $this->ordonnacesMedecin[] = $ordonnacesMedecin;
            $ordonnacesMedecin->setMedecinTraitant($this);
        }

        return $this;
    }

    public function removeOrdonnacesMedecin(Ordonnace $ordonnacesMedecin): self
    {
        if ($this->ordonnacesMedecin->contains($ordonnacesMedecin)) {
            $this->ordonnacesMedecin->removeElement($ordonnacesMedecin);
            // set the owning side to null (unless already changed)
            if ($ordonnacesMedecin->getMedecinTraitant() === $this) {
                $ordonnacesMedecin->setMedecinTraitant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntervationMedicale[]
     */
    public function getIntervationMedicales(): Collection
    {
        return $this->intervationMedicales;
    }

    public function addIntervationMedicale(IntervationMedicale $intervationMedicale): self
    {
        if (!$this->intervationMedicales->contains($intervationMedicale)) {
            $this->intervationMedicales[] = $intervationMedicale;
            $intervationMedicale->setPraticien($this);
        }

        return $this;
    }

    public function removeIntervationMedicale(IntervationMedicale $intervationMedicale): self
    {
        if ($this->intervationMedicales->contains($intervationMedicale)) {
            $this->intervationMedicales->removeElement($intervationMedicale);
            // set the owning side to null (unless already changed)
            if ($intervationMedicale->getPraticien() === $this) {
                $intervationMedicale->setPraticien(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InterventionVaccination[]
     */
    public function getInterventionVaccinations(): Collection
    {
        return $this->interventionVaccinations;
    }

    public function addInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if (!$this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations[] = $interventionVaccination;
            $interventionVaccination->setPraticienPrescripteur($this);
        }

        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getPraticienPrescripteur() === $this) {
                $interventionVaccination->setPraticienPrescripteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InterventionVaccination[]
     */
    public function getInterventionExecutant(): Collection
    {
        return $this->interventionExecutant;
    }

    public function addInterventionExecutant(InterventionVaccination $interventionExecutant): self
    {
        if (!$this->interventionExecutant->contains($interventionExecutant)) {
            $this->interventionExecutant[] = $interventionExecutant;
            $interventionExecutant->setPraticienExecutant($this);
        }

        return $this;
    }

    public function removeInterventionExecutant(InterventionVaccination $interventionExecutant): self
    {
        if ($this->interventionExecutant->contains($interventionExecutant)) {
            $this->interventionExecutant->removeElement($interventionExecutant);
            // set the owning side to null (unless already changed)
            if ($interventionExecutant->getPraticienExecutant() === $this) {
                $interventionExecutant->setPraticienExecutant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdoVaccination[]
     */
    public function getOrdoVaccinationPraticienExecutant(): Collection
    {
        return $this->ordoVaccinationPraticienExecutant;
    }

    public function addOrdoVaccinationPraticienExecutant(OrdoVaccination $ordoVaccinationPraticienExecutant): self
    {
        if (!$this->ordoVaccinationPraticienExecutant->contains($ordoVaccinationPraticienExecutant)) {
            $this->ordoVaccinationPraticienExecutant[] = $ordoVaccinationPraticienExecutant;
            $ordoVaccinationPraticienExecutant->setReferencePraticienExecutant($this);
        }

        return $this;
    }

    public function removeOrdoVaccinationPraticienExecutant(OrdoVaccination $ordoVaccinationPraticienExecutant): self
    {
        if ($this->ordoVaccinationPraticienExecutant->contains($ordoVaccinationPraticienExecutant)) {
            $this->ordoVaccinationPraticienExecutant->removeElement($ordoVaccinationPraticienExecutant);
            // set the owning side to null (unless already changed)
            if ($ordoVaccinationPraticienExecutant->getReferencePraticienExecutant() === $this) {
                $ordoVaccinationPraticienExecutant->setReferencePraticienExecutant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntervationConsultation[]
     */
    public function getIntervationConsultationsPraticienPrescripteur(): Collection
    {
        return $this->intervationConsultationsPraticienPrescripteur;
    }

    public function addIntervationConsultationsPraticienPrescripteur(IntervationConsultation $intervationConsultationsPraticienPrescripteur): self
    {
        if (!$this->intervationConsultationsPraticienPrescripteur->contains($intervationConsultationsPraticienPrescripteur)) {
            $this->intervationConsultationsPraticienPrescripteur[] = $intervationConsultationsPraticienPrescripteur;
            $intervationConsultationsPraticienPrescripteur->setPraticienPrescripteur($this);
        }

        return $this;
    }

    public function removeIntervationConsultationsPraticienPrescripteur(IntervationConsultation $intervationConsultationsPraticienPrescripteur): self
    {
        if ($this->intervationConsultationsPraticienPrescripteur->contains($intervationConsultationsPraticienPrescripteur)) {
            $this->intervationConsultationsPraticienPrescripteur->removeElement($intervationConsultationsPraticienPrescripteur);
            // set the owning side to null (unless already changed)
            if ($intervationConsultationsPraticienPrescripteur->getPraticienPrescripteur() === $this) {
                $intervationConsultationsPraticienPrescripteur->setPraticienPrescripteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntervationConsultation[]
     */
    public function getIntervationConsultationsPraticienConsultant(): Collection
    {
        return $this->intervationConsultationsPraticienConsultant;
    }

    public function addIntervationConsultationsPraticienConsultant(IntervationConsultation $intervationConsultationsPraticienConsultant): self
    {
        if (!$this->intervationConsultationsPraticienConsultant->contains($intervationConsultationsPraticienConsultant)) {
            $this->intervationConsultationsPraticienConsultant[] = $intervationConsultationsPraticienConsultant;
            $intervationConsultationsPraticienConsultant->setPraticienConsultant($this);
        }

        return $this;
    }

    public function removeIntervationConsultationsPraticienConsultant(IntervationConsultation $intervationConsultationsPraticienConsultant): self
    {
        if ($this->intervationConsultationsPraticienConsultant->contains($intervationConsultationsPraticienConsultant)) {
            $this->intervationConsultationsPraticienConsultant->removeElement($intervationConsultationsPraticienConsultant);
            // set the owning side to null (unless already changed)
            if ($intervationConsultationsPraticienConsultant->getPraticienConsultant() === $this) {
                $intervationConsultationsPraticienConsultant->setPraticienConsultant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VaccinPraticien[]
     */
    public function getVaccinPraticiens(): Collection
    {
        return $this->vaccinPraticiens;
    }

    public function addVaccinPraticien(VaccinPraticien $vaccinPraticien): self
    {
        if (!$this->vaccinPraticiens->contains($vaccinPraticien)) {
            $this->vaccinPraticiens[] = $vaccinPraticien;
            $vaccinPraticien->setPraticien($this);
        }

        return $this;
    }

    public function removeVaccinPraticien(VaccinPraticien $vaccinPraticien): self
    {
        if ($this->vaccinPraticiens->contains($vaccinPraticien)) {
            $this->vaccinPraticiens->removeElement($vaccinPraticien);
            // set the owning side to null (unless already changed)
            if ($vaccinPraticien->getPraticien() === $this) {
                $vaccinPraticien->setPraticien(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PraticienSpecialite[]
     */
    public function getPraticienSpecialites(): Collection
    {
        return $this->praticienSpecialites;
    }

    public function addPraticienSpecialite(PraticienSpecialite $praticienSpecialite): self
    {
        if (!$this->praticienSpecialites->contains($praticienSpecialite)) {
            $this->praticienSpecialites[] = $praticienSpecialite;
            $praticienSpecialite->setPraticien($this);
        }

        return $this;
    }

    public function removePraticienSpecialite(PraticienSpecialite $praticienSpecialite): self
    {
        if ($this->praticienSpecialites->contains($praticienSpecialite)) {
            $this->praticienSpecialites->removeElement($praticienSpecialite);
            // set the owning side to null (unless already changed)
            if ($praticienSpecialite->getPraticien() === $this) {
                $praticienSpecialite->setPraticien(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function __toString()
    {
        return $this->getLastName() .' '. $this->getFirstName();
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

}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PraticienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PraticienRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:praticien"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class Praticien
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:VaccinPraticien","read:praticien","read:OrdoConsultation", "read:PropositionRdv", "read:associer"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:VaccinPraticien","read:praticien", "read:carnetvaccination", "read:IntervationConsultation", "read:PropositionRdv", "read:associer"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:VaccinPraticien", "read:praticien", "read:carnetvaccination", "read:IntervationConsultation", "read:PropositionRdv", "read:associer"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:praticien"})
     */
    private $phone;



    /**
     * @ORM\Column(type="date")
     * @Groups({"read:praticien"})
     */
    private $dateBorn;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:praticien"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:praticien"})
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @Groups({"read:praticien"})
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
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:praticien"})
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:praticien"})
     */
    private $numRue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:praticien"})
     */
    private $quartier;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="citypraticien")
     * @Groups({"read:praticien"})
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=PropositionRdv::class, mappedBy="praticien")
     */
    private $propositionRdvs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adressOnBorn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="praticiens")
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=Associer::class, mappedBy="praticien")
     */
    private $associers;

    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    private $NumeroProfessionnel;

    /**
     * @ORM\OneToMany(targetEntity=InterventionVaccination::class, mappedBy="Praticien")
     */
    private $interventionVaccinations;


    /**
     * @ORM\OneToMany(targetEntity=CarnetVaccination::class, mappedBy="Praticien")
     */
    private $carnetVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=Fonction::class, mappedBy="Praticien")
     */
    private $fonctions;





    public function __construct()
    {
        $this->updatedAt = new \DateTime('now');
        $this->createdAt = new \DateTime('now');
        $this->ordonnaces = new ArrayCollection();
        $this->ordonnacesMedecin = new ArrayCollection();
        $this->intervationMedicales = new ArrayCollection();
        $this->ordoVaccinationPraticienExecutant = new ArrayCollection();
        $this->intervationConsultationsPraticienPrescripteur = new ArrayCollection();
        $this->intervationConsultationsPraticienConsultant = new ArrayCollection();
        $this->vaccinPraticiens = new ArrayCollection();
        $this->praticienSpecialites = new ArrayCollection();
        $this->etat = false;
        $this->propositionRdvs = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->associers = new ArrayCollection();
        $this->interventionVaccinations = new ArrayCollection();
        $this->carnetVaccinations = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
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

    public function getNumRue(): ?string
    {
        return $this->numRue;
    }

    public function setNumRue(?string $numRue): self
    {
        $this->numRue = $numRue;

        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->quartier;
    }

    public function setQuartier(?string $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|PropositionRdv[]
     */
    public function getPropositionRdvs(): Collection
    {
        return $this->propositionRdvs;
    }

    public function addPropositionRdv(PropositionRdv $propositionRdv): self
    {
        if (!$this->propositionRdvs->contains($propositionRdv)) {
            $this->propositionRdvs[] = $propositionRdv;
            $propositionRdv->setPraticien($this);
        }

        return $this;
    }

    public function removePropositionRdv(PropositionRdv $propositionRdv): self
    {
        if ($this->propositionRdvs->contains($propositionRdv)) {
            $this->propositionRdvs->removeElement($propositionRdv);
            // set the owning side to null (unless already changed)
            if ($propositionRdv->getPraticien() === $this) {
                $propositionRdv->setPraticien(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAdressOnBorn(): ?string
    {
        return $this->adressOnBorn;
    }

    public function setAdressOnBorn(?string $adressOnBorn): self
    {
        $this->adressOnBorn = $adressOnBorn;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

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

    /**
     * @return Collection|Associer[]
     */
    public function getAssociers(): Collection
    {
        return $this->associers;
    }

    public function addAssocier(Associer $associer): self
    {
        if (!$this->associers->contains($associer)) {
            $this->associers[] = $associer;
            $associer->setPraticien($this);
        }

        return $this;
    }

    public function removeAssocier(Associer $associer): self
    {
        if ($this->associers->contains($associer)) {
            $this->associers->removeElement($associer);
            // set the owning side to null (unless already changed)
            if ($associer->getPraticien() === $this) {
                $associer->setPraticien(null);
            }
        }

        return $this;
    }

    public function getNumeroProfessionnel(): ?string
    {
        return $this->NumeroProfessionnel;
    }

    public function setNumeroProfessionnel(string $NumeroProfessionnel): self
    {
        $this->NumeroProfessionnel = $NumeroProfessionnel;

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
            $interventionVaccination->setPraticien($this);
        }

        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getPraticien() === $this) {
                $interventionVaccination->setPraticien(null);
            }
        }

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * @return Collection|CarnetVaccination[]
     */
    public function getCarnetVaccinations(): Collection
    {
        return $this->carnetVaccinations;
    }

    public function addCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if (!$this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations[] = $carnetVaccination;
            $carnetVaccination->setPraticien($this);
        }

        return $this;
    }

    public function removeCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if ($this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations->removeElement($carnetVaccination);
            // set the owning side to null (unless already changed)
            if ($carnetVaccination->getPraticien() === $this) {
                $carnetVaccination->setPraticien(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fonction[]
     */
    public function getFonctions(): Collection
    {
        return $this->fonctions;
    }

    public function addFonction(Fonction $fonction): self
    {
        if (!$this->fonctions->contains($fonction)) {
            $this->fonctions[] = $fonction;
            $fonction->setPraticien($this);
        }

        return $this;
    }

    public function removeFonction(Fonction $fonction): self
    {
        if ($this->fonctions->contains($fonction)) {
            $this->fonctions->removeElement($fonction);
            // set the owning side to null (unless already changed)
            if ($fonction->getPraticien() === $this) {
                $fonction->setPraticien(null);
            }
        }

        return $this;
    }

    
}
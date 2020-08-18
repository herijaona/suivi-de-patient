<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass=PatientRepository::class)
 *  @ApiResource(
 *    normalizationContext={"groups"={"read:patient"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class Patient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:patient","read:OrdoConsultation", "read:Family", "read:PropositionRdv"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:patient", "read:carnetvaccination", "read:Family", "read:PropositionRdv"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"read:patient", "read:carnetvaccination", "read:Family", "read:PropositionRdv"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:patient"})
     */
    private $sexe;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:patient"})
     */
    private $dateOnBorn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fatherName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motherName;


    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read:patient"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=TypePatient::class, inversedBy="typePatient")
     */
    private $typePatient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:patient"})
     */
    private $phone;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEnceinte;

    /**
     * @ORM\OneToMany(targetEntity=GroupFamily::class, mappedBy="patient")
     */
    private $groupFamily;

    /**
     * @ORM\OneToMany(targetEntity=Family::class, mappedBy="patientChild")
     */
    private $familyChild;

    /**
     * @ORM\OneToMany(targetEntity=OrdoVaccination::class, mappedBy="patient")
     */
    private $ordoVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoConsultation::class, mappedBy="patient")
     */
    private $ordoConsultations;

    /**
     * @ORM\OneToMany(targetEntity=OrdoMedicaments::class, mappedBy="patient")
     */
    private $ordoMedicaments;

    /**
     * @ORM\OneToMany(targetEntity=CarnetVaccination::class, mappedBy="patient")
     */
    private $carnetVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=IntervationConsultation::class, mappedBy="patient")
     */
    private $intervationConsultations;

    /**
     * @ORM\OneToMany(targetEntity=PatientCarnetVaccination::class, mappedBy="patient")
     */
    private $patientCarnetVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoConsultation::class, mappedBy="patient")
     */
    private $patientOrdoConsultations;

    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoMedicaments::class, mappedBy="patient")
     */
    private $patientOrdoMedicaments;

    /**
     * @ORM\OneToMany(targetEntity=PatientOrdoVaccination::class, mappedBy="patient")
     */
    private $patientOrdoVaccinations;

    /**
     * @ORM\OneToMany(targetEntity=PatientIntervationConsultation::class, mappedBy="patient")
     */
    private $patientIntervationConsultations;

    /**
     * @ORM\OneToMany(targetEntity=PatientVaccin::class, mappedBy="patient")
     */
    private $patientVaccins;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="patient")
     */
        private $address;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="patients")
     */
    private $addressOnBorn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numRue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quartier;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="citypatient")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=PropositionRdv::class, mappedBy="patient")
     */
    private $propositionRdvs;



    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
        $this->setUpdatedAt(new \DateTime('now'));
        $this->families = new ArrayCollection();
        $this->familyChild = new ArrayCollection();
        $this->groupFamily = new ArrayCollection();
        $this->ordoVaccinations = new ArrayCollection();
        $this->ordoConsultations = new ArrayCollection();
        $this->ordoMedicaments = new ArrayCollection();
        $this->carnetVaccinations = new ArrayCollection();
        $this->intervationConsultations = new ArrayCollection();
        $this->patientCarnetVaccinations = new ArrayCollection();
        $this->patientOrdoConsultations = new ArrayCollection();
        $this->patientOrdoMedicaments = new ArrayCollection();
        $this->patientOrdoVaccinations = new ArrayCollection();
        $this->patientIntervationConsultations = new ArrayCollection();
        $this->patientVaccins = new ArrayCollection();
        $this->etat = false;
        $this->interventionVaccinations = new ArrayCollection();
        $this->propositionRdvs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

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

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateOnBorn(): ?\DateTimeInterface
    {
        return $this->dateOnBorn;
    }

    public function setDateOnBorn(\DateTimeInterface $dateOnBorn): self
    {
        $this->dateOnBorn = $dateOnBorn;

        return $this;
    }

    public function getFatherName(): ?string
    {
        return $this->fatherName;
    }

    public function setFatherName(?string $father_name): self
    {
        $this->fatherName = $father_name;

        return $this;
    }

    public function getMotherName(): ?string
    {
        return $this->motherName;
    }

    public function setMotherName(?string $mother_name): self
    {
        $this->motherName = $mother_name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    public function getTypePatient(): ?TypePatient
    {
        return $this->typePatient;
    }

    public function setTypePatient(?TypePatient $type_patient): self
    {
        $this->typePatient = $type_patient;

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

    /**
     * @return Collection|Family[]
     */
    public function getFamilyChild(): Collection
    {
        return $this->familyChild;
    }

    public function addFamilyChild(Family $familyChild): self
    {
        if (!$this->familyChild->contains($familyChild)) {
            $this->familyChild[] = $familyChild;
            $familyChild->setPatientChild($this);
        }

        return $this;
    }

    public function removeFamilyChild(Family $familyChild): self
    {
        if ($this->familyChild->contains($familyChild)) {
            $this->familyChild->removeElement($familyChild);
            // set the owning side to null (unless already changed)
            if ($familyChild->getPatientChild() === $this) {
                $familyChild->setPatientChild(null);
            }
        }

        return $this;
    }

    public function getIsEnceinte(): ?bool
    {
        return $this->isEnceinte;
    }

    public function setIsEnceinte(?bool $is_enceinte): self
    {
        $this->isEnceinte = $is_enceinte;

        return $this;
    }

    /**
     * @return Collection|GroupFamily[]
     */
    public function getGroupFamily(): Collection
    {
        return $this->groupFamily;
    }

    public function addGroupFamily(GroupFamily $groupFamily): self
    {
        if (!$this->groupFamily->contains($groupFamily)) {
            $this->groupFamily[] = $groupFamily;
            $groupFamily->setGroupFamily($this);
        }

        return $this;
    }

    public function removeGroupFamily(GroupFamily $groupFamily): self
    {
        if ($this->groupFamily->contains($groupFamily)) {
            $this->groupFamily->removeElement($groupFamily);
            // set the owning side to null (unless already changed)
            if ($groupFamily->getPatient() === $this) {
                $groupFamily->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdoVaccination[]
     */
    public function getOrdoVaccinations(): Collection
    {
        return $this->ordoVaccinations;
    }

    public function addOrdoVaccination(OrdoVaccination $ordoVaccination): self
    {
        if (!$this->ordoVaccinations->contains($ordoVaccination)) {
            $this->ordoVaccinations[] = $ordoVaccination;
            $ordoVaccination->setPatient($this);
        }

        return $this;
    }

    public function removeOrdoVaccination(OrdoVaccination $ordoVaccination): self
    {
        if ($this->ordoVaccinations->contains($ordoVaccination)) {
            $this->ordoVaccinations->removeElement($ordoVaccination);
            // set the owning side to null (unless already changed)
            if ($ordoVaccination->getPatient() === $this) {
                $ordoVaccination->setPatient(null);
            }
        }

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
            $ordoConsultation->setPatient($this);
        }

        return $this;
    }

    public function removeOrdoConsultation(OrdoConsultation $ordoConsultation): self
    {
        if ($this->ordoConsultations->contains($ordoConsultation)) {
            $this->ordoConsultations->removeElement($ordoConsultation);
            // set the owning side to null (unless already changed)
            if ($ordoConsultation->getPatient() === $this) {
                $ordoConsultation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrdoMedicaments[]
     */
    public function getOrdoMedicaments(): Collection
    {
        return $this->ordoMedicaments;
    }

    public function addOrdoMedicament(OrdoMedicaments $ordoMedicament): self
    {
        if (!$this->ordoMedicaments->contains($ordoMedicament)) {
            $this->ordoMedicaments[] = $ordoMedicament;
            $ordoMedicament->setPatient($this);
        }

        return $this;
    }

    public function removeOrdoMedicament(OrdoMedicaments $ordoMedicament): self
    {
        if ($this->ordoMedicaments->contains($ordoMedicament)) {
            $this->ordoMedicaments->removeElement($ordoMedicament);
            // set the owning side to null (unless already changed)
            if ($ordoMedicament->getPatient() === $this) {
                $ordoMedicament->setPatient(null);
            }
        }

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
            $carnetVaccination->setPatient($this);
        }

        return $this;
    }

    public function removeCarnetVaccination(CarnetVaccination $carnetVaccination): self
    {
        if ($this->carnetVaccinations->contains($carnetVaccination)) {
            $this->carnetVaccinations->removeElement($carnetVaccination);
            // set the owning side to null (unless already changed)
            if ($carnetVaccination->getPatient() === $this) {
                $carnetVaccination->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntervationConsultation[]
     */
    public function getIntervationConsultations(): Collection
    {
        return $this->intervationConsultations;
    }

    public function addIntervationConsultation(IntervationConsultation $intervationConsultation): self
    {
        if (!$this->intervationConsultations->contains($intervationConsultation)) {
            $this->intervationConsultations[] = $intervationConsultation;
            $intervationConsultation->setPatient($this);
        }

        return $this;
    }

    public function removeIntervationConsultation(IntervationConsultation $intervationConsultation): self
    {
        if ($this->intervationConsultations->contains($intervationConsultation)) {
            $this->intervationConsultations->removeElement($intervationConsultation);
            // set the owning side to null (unless already changed)
            if ($intervationConsultation->getPatient() === $this) {
                $intervationConsultation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PatientCarnetVaccination[]
     */
    public function getPatientCarnetVaccinations(): Collection
    {
        return $this->patientCarnetVaccinations;
    }

    public function addPatientCarnetVaccination(PatientCarnetVaccination $patientCarnetVaccination): self
    {
        if (!$this->patientCarnetVaccinations->contains($patientCarnetVaccination)) {
            $this->patientCarnetVaccinations[] = $patientCarnetVaccination;
            $patientCarnetVaccination->setPatient($this);
        }

        return $this;
    }

    public function removePatientCarnetVaccination(PatientCarnetVaccination $patientCarnetVaccination): self
    {
        if ($this->patientCarnetVaccinations->contains($patientCarnetVaccination)) {
            $this->patientCarnetVaccinations->removeElement($patientCarnetVaccination);
            // set the owning side to null (unless already changed)
            if ($patientCarnetVaccination->getPatient() === $this) {
                $patientCarnetVaccination->setPatient(null);
            }
        }

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
            $patientOrdoConsultation->setPatient($this);
        }

        return $this;
    }

    public function removePatientOrdoConsultation(PatientOrdoConsultation $patientOrdoConsultation): self
    {
        if ($this->patientOrdoConsultations->contains($patientOrdoConsultation)) {
            $this->patientOrdoConsultations->removeElement($patientOrdoConsultation);
            // set the owning side to null (unless already changed)
            if ($patientOrdoConsultation->getPatient() === $this) {
                $patientOrdoConsultation->setPatient(null);
            }
        }

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
            $patientOrdoMedicament->setPatient($this);
        }

        return $this;
    }

    public function removePatientOrdoMedicament(PatientOrdoMedicaments $patientOrdoMedicament): self
    {
        if ($this->patientOrdoMedicaments->contains($patientOrdoMedicament)) {
            $this->patientOrdoMedicaments->removeElement($patientOrdoMedicament);
            // set the owning side to null (unless already changed)
            if ($patientOrdoMedicament->getPatient() === $this) {
                $patientOrdoMedicament->setPatient(null);
            }
        }

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
            $patientOrdoVaccination->setPatient($this);
        }

        return $this;
    }

    public function removePatientOrdoVaccination(PatientOrdoVaccination $patientOrdoVaccination): self
    {
        if ($this->patientOrdoVaccinations->contains($patientOrdoVaccination)) {
            $this->patientOrdoVaccinations->removeElement($patientOrdoVaccination);
            // set the owning side to null (unless already changed)
            if ($patientOrdoVaccination->getPatient() === $this) {
                $patientOrdoVaccination->setPatient(null);
            }
        }

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
            $patientIntervationConsultation->setPatient($this);
        }

        return $this;
    }

    public function removePatientIntervationConsultation(PatientIntervationConsultation $patientIntervationConsultation): self
    {
        if ($this->patientIntervationConsultations->contains($patientIntervationConsultation)) {
            $this->patientIntervationConsultations->removeElement($patientIntervationConsultation);
            // set the owning side to null (unless already changed)
            if ($patientIntervationConsultation->getPatient() === $this) {
                $patientIntervationConsultation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PatientVaccin[]
     */
    public function getPatientVaccins(): Collection
    {
        return $this->patientVaccins;
    }

    public function addPatientVaccin(PatientVaccin $patientVaccin): self
    {
        if (!$this->patientVaccins->contains($patientVaccin)) {
            $this->patientVaccins[] = $patientVaccin;
            $patientVaccin->setPatient($this);
        }

        return $this;
    }

    public function removePatientVaccin(PatientVaccin $patientVaccin): self
    {
        if ($this->patientVaccins->contains($patientVaccin)) {
            $this->patientVaccins->removeElement($patientVaccin);
            // set the owning side to null (unless already changed)
            if ($patientVaccin->getPatient() === $this) {
                $patientVaccin->setPatient(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?City
    {
        return $this->address;
    }

    public function setAddress(?City $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAddressOnBorn(): ?City
    {
        return $this->addressOnBorn;
    }

    public function setAddressOnBorn(?City $addressOnBorn): self
    {
        $this->addressOnBorn = $addressOnBorn;

        return $this;
    }

    public function __toString()
    {
        return $this->getLastName() .' '. $this->getFirstName();
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
            $interventionVaccination->setPatient($this);
        }
        return $this;
    }

    public function removeInterventionVaccination(InterventionVaccination $interventionVaccination): self
    {
        if ($this->interventionVaccinations->contains($interventionVaccination)) {
            $this->interventionVaccinations->removeElement($interventionVaccination);
            // set the owning side to null (unless already changed)
            if ($interventionVaccination->getPatient() === $this) {
                $interventionVaccination->setPatient(null);
            }
        }
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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
            $propositionRdv->setPatient($this);
        }

        return $this;
    }

    public function removePropositionRdv(PropositionRdv $propositionRdv): self
    {
        if ($this->propositionRdvs->contains($propositionRdv)) {
            $this->propositionRdvs->removeElement($propositionRdv);
            // set the owning side to null (unless already changed)
            if ($propositionRdv->getPatient() === $this) {
                $propositionRdv->setPatient(null);
            }
        }

        return $this;
    }

}
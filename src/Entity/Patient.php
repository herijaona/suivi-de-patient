<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientRepository::class)
 */
class Patient
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
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    /**
     * @ORM\Column(type="date")
     */
    private $date_on_born;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $father_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mother_name;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="type_patient")
     */
    private $adress;


    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="patients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $adress_on_born;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=TypePatient::class, inversedBy="type_patient")
     */
    private $type_patient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=RendezVous::class, mappedBy="patient")
     */
    private $rendeVous;

    /**
     * @ORM\OneToMany(targetEntity=Family::class, mappedBy="patient_parent")
     */
    private $families;

    /**
     * @ORM\OneToMany(targetEntity=Family::class, mappedBy="patient_child")
     */
    private $family_child;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_enceinte;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        $this->rendeVous = new ArrayCollection();
        $this->families = new ArrayCollection();
        $this->family_child = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

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
        return $this->date_on_born;
    }

    public function setDateOnBorn(\DateTimeInterface $date_on_born): self
    {
        $this->date_on_born = $date_on_born;

        return $this;
    }

    public function getFatherName(): ?string
    {
        return $this->father_name;
    }

    public function setFatherName(?string $father_name): self
    {
        $this->father_name = $father_name;

        return $this;
    }

    public function getMotherName(): ?string
    {
        return $this->mother_name;
    }

    public function setMotherName(?string $mother_name): self
    {
        $this->mother_name = $mother_name;

        return $this;
    }

    public function getAdress(): ?City
    {
        return $this->adress;
    }

    public function setAdress(?City $adress): self
    {
        $this->adress = $adress;

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

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getAdressOnBorn(): ?City
    {
        return $this->adress_on_born;
    }

    public function setAdressOnBorn(?City $adress_on_born): self
    {
        $this->adress_on_born = $adress_on_born;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTypePatient(): ?TypePatient
    {
        return $this->type_patient;
    }

    public function setTypePatient(?TypePatient $type_patient): self
    {
        $this->type_patient = $type_patient;

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
     * @return Collection|RendezVous[]
     */
    public function getRendeVous(): Collection
    {
        return $this->rendeVous;
    }

    public function addRendeVou(RendezVous $rendeVou): self
    {
        if (!$this->rendeVous->contains($rendeVou)) {
            $this->rendeVous[] = $rendeVou;
            $rendeVou->setPatient($this);
        }

        return $this;
    }

    public function removeRendeVou(RendezVous $rendeVou): self
    {
        if ($this->rendeVous->contains($rendeVou)) {
            $this->rendeVous->removeElement($rendeVou);
            // set the owning side to null (unless already changed)
            if ($rendeVou->getPatient() === $this) {
                $rendeVou->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Family[]
     */
    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): self
    {
        if (!$this->families->contains($family)) {
            $this->families[] = $family;
            $family->setPatientParent($this);
        }

        return $this;
    }

    public function removeFamily(Family $family): self
    {
        if ($this->families->contains($family)) {
            $this->families->removeElement($family);
            // set the owning side to null (unless already changed)
            if ($family->getPatientParent() === $this) {
                $family->setPatientParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Family[]
     */
    public function getFamilyChild(): Collection
    {
        return $this->family_child;
    }

    public function addFamilyChild(Family $familyChild): self
    {
        if (!$this->family_child->contains($familyChild)) {
            $this->family_child[] = $familyChild;
            $familyChild->setPatientChild($this);
        }

        return $this;
    }

    public function removeFamilyChild(Family $familyChild): self
    {
        if ($this->family_child->contains($familyChild)) {
            $this->family_child->removeElement($familyChild);
            // set the owning side to null (unless already changed)
            if ($familyChild->getPatientChild() === $this) {
                $familyChild->setPatientChild(null);
            }
        }

        return $this;
    }

    public function getIsEnceinte(): ?bool
    {
        return $this->is_enceinte;
    }

    public function setIsEnceinte(?bool $is_enceinte): self
    {
        $this->is_enceinte = $is_enceinte;

        return $this;
    }
}

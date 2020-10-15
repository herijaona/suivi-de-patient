<?php

namespace App\Entity;

use App\Repository\CityRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:city"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:city"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:city"})
     */
    private $nameCity;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="cities")
     *  @Groups({"read:city"})
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="ville")
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="address")
     */
    private $patient;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="addressOnBorn")
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="address")
     */
    private $praticien;

    /**
     * @ORM\OneToMany(targetEntity=CentreHealth::class, mappedBy="city")
     */
    private $centreHealths;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="city")
     */
    private $citypatient;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="city")
     */
    private $citypraticien;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Arrondissement;

    /**
     * @ORM\OneToMany(targetEntity=Fonction::class, mappedBy="city")
     */
    private $fonctions;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="CityOnBorn")
     */
    private $patientBorn;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="CityOnBorn")
     */
    private $praticiencityborn;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="CityFonction")
     */
    private $praticiens;




    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->centreHealths = new ArrayCollection();
        $this->patient = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->praticien = new ArrayCollection();
        $this->citypatient = new ArrayCollection();
        $this->citypraticien = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
        $this->patientBorn = new ArrayCollection();
        $this->praticiencityborn = new ArrayCollection();
        $this->praticiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCity(): ?string
    {
        return $this->nameCity;
    }

    public function setNameCity(string $nameCity): self
    {
        $this->nameCity = $nameCity;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setVille($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getVille() === $this) {
                $address->setVille(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNameCity();
    }

    /**
     * @return Collection|CentreHealth[]
     */
    public function getCentreHealths(): Collection
    {
        return $this->centreHealths;
    }

    public function addCentreHealth(CentreHealth $centreHealth): self
    {
        if (!$this->centreHealths->contains($centreHealth)) {
            $this->centreHealths[] = $centreHealth;
            $centreHealth->setCity($this);
        }

        return $this;
    }

    public function removeCentreHealth(CentreHealth $centreHealth): self
    {
        if ($this->centreHealths->contains($centreHealth)) {
            $this->centreHealths->removeElement($centreHealth);
            // set the owning side to null (unless already changed)
            if ($centreHealth->getCity() === $this) {
                $centreHealth->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatient(): Collection
    {
        return $this->patient;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patient->contains($patient)) {
            $this->patient[] = $patient;
            $patient->setAddress($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patient->contains($patient)) {
            $this->patient->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getAddress() === $this) {
                $patient->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatients(Patient $patients): self
    {
        if (!$this->patients->contains($patients)) {
            $this->patients[] = $patients;
            $patients->setAddressOnBorn($this);
        }

        return $this;
    }

    public function removePatients(Patient $patients): self
    {
        if ($this->patients->contains($patients)) {
            $this->patients->removeElement($patients);
            // set the owning side to null (unless already changed)
            if ($patients->getAddressOnBorn() === $this) {
                $patients->setAddressOnBorn(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getPraticien(): Collection
    {
        return $this->praticien;
    }

    public function addPraticien(Praticien $praticien): self
    {
        if (!$this->praticien->contains($praticien)) {
            $this->praticien[] = $praticien;
            $praticien->setAddress($this);
        }

        return $this;
    }

    public function removePraticien(Praticien $praticien): self
    {
        if ($this->praticien->contains($praticien)) {
            $this->praticien->removeElement($praticien);
            // set the owning side to null (unless already changed)
            if ($praticien->getAddress() === $this) {
                $praticien->setAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getCitypatient(): Collection
    {
        return $this->citypatient;
    }

    public function addCitypatient(Patient $citypatient): self
    {
        if (!$this->citypatient->contains($citypatient)) {
            $this->citypatient[] = $citypatient;
            $citypatient->setCity($this);
        }

        return $this;
    }

    public function removeCitypatient(Patient $citypatient): self
    {
        if ($this->citypatient->contains($citypatient)) {
            $this->citypatient->removeElement($citypatient);
            // set the owning side to null (unless already changed)
            if ($citypatient->getCity() === $this) {
                $citypatient->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getCitypraticien(): Collection
    {
        return $this->citypraticien;
    }

    public function addCitypraticien(Praticien $citypraticien): self
    {
        if (!$this->citypraticien->contains($citypraticien)) {
            $this->citypraticien[] = $citypraticien;
            $citypraticien->setCity($this);
        }

        return $this;
    }

    public function removeCitypraticien(Praticien $citypraticien): self
    {
        if ($this->citypraticien->contains($citypraticien)) {
            $this->citypraticien->removeElement($citypraticien);
            // set the owning side to null (unless already changed)
            if ($citypraticien->getCity() === $this) {
                $citypraticien->setCity(null);
            }
        }

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getArrondissement(): ?string
    {
        return $this->Arrondissement;
    }

    public function setArrondissement(?string $Arrondissement): self
    {
        $this->Arrondissement = $Arrondissement;

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
            $fonction->setCity($this);
        }

        return $this;
    }

    public function removeFonction(Fonction $fonction): self
    {
        if ($this->fonctions->contains($fonction)) {
            $this->fonctions->removeElement($fonction);
            // set the owning side to null (unless already changed)
            if ($fonction->getCity() === $this) {
                $fonction->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatientBorn(): Collection
    {
        return $this->patientBorn;
    }

    public function addPatientBorn(Patient $patientBorn): self
    {
        if (!$this->patientBorn->contains($patientBorn)) {
            $this->patientBorn[] = $patientBorn;
            $patientBorn->setCityOnBorn($this);
        }

        return $this;
    }

    public function removePatientBorn(Patient $patientBorn): self
    {
        if ($this->patientBorn->contains($patientBorn)) {
            $this->patientBorn->removeElement($patientBorn);
            // set the owning side to null (unless already changed)
            if ($patientBorn->getCityOnBorn() === $this) {
                $patientBorn->setCityOnBorn(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getPraticiencityborn(): Collection
    {
        return $this->praticiencityborn;
    }

    public function addPraticiencityborn(Praticien $praticiencityborn): self
    {
        if (!$this->praticiencityborn->contains($praticiencityborn)) {
            $this->praticiencityborn[] = $praticiencityborn;
            $praticiencityborn->setCityOnBorn($this);
        }

        return $this;
    }

    public function removePraticiencityborn(Praticien $praticiencityborn): self
    {
        if ($this->praticiencityborn->contains($praticiencityborn)) {
            $this->praticiencityborn->removeElement($praticiencityborn);
            // set the owning side to null (unless already changed)
            if ($praticiencityborn->getCityOnBorn() === $this) {
                $praticiencityborn->setCityOnBorn(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getPraticiens(): Collection
    {
        return $this->praticiens;
    }

}
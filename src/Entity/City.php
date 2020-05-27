<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
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
    private $name_city;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="cities")
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="adress")
     */
    private $type_patient;

    /**
     * @ORM\OneToMany(targetEntity=Patient::class, mappedBy="adress_on_born")
     */
    private $patients;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="adress")
     */
    private $praticiens;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="adress_born")
     */
    private $city_born;

    public function __construct()
    {
        $this->type_patient = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->praticiens = new ArrayCollection();
        $this->city_born = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCity(): ?string
    {
        return $this->name_city;
    }

    public function setNameCity(string $name_city): self
    {
        $this->name_city = $name_city;

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
     * @return Collection|Patient[]
     */
    public function getTypePatient(): Collection
    {
        return $this->type_patient;
    }

    public function addTypePatient(Patient $typePatient): self
    {
        if (!$this->type_patient->contains($typePatient)) {
            $this->type_patient[] = $typePatient;
            $typePatient->setAdress($this);
        }

        return $this;
    }

    public function removeTypePatient(Patient $typePatient): self
    {
        if ($this->type_patient->contains($typePatient)) {
            $this->type_patient->removeElement($typePatient);
            // set the owning side to null (unless already changed)
            if ($typePatient->getAdress() === $this) {
                $typePatient->setAdress(null);
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

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setAdressOnBorn($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getAdressOnBorn() === $this) {
                $patient->setAdressOnBorn(null);
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

    public function addPraticien(Praticien $praticien): self
    {
        if (!$this->praticiens->contains($praticien)) {
            $this->praticiens[] = $praticien;
            $praticien->setAdress($this);
        }

        return $this;
    }

    public function removePraticien(Praticien $praticien): self
    {
        if ($this->praticiens->contains($praticien)) {
            $this->praticiens->removeElement($praticien);
            // set the owning side to null (unless already changed)
            if ($praticien->getAdress() === $this) {
                $praticien->setAdress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Praticien[]
     */
    public function getCityBorn(): Collection
    {
        return $this->city_born;
    }

    public function addCityBorn(Praticien $cityBorn): self
    {
        if (!$this->city_born->contains($cityBorn)) {
            $this->city_born[] = $cityBorn;
            $cityBorn->setAdressBorn($this);
        }

        return $this;
    }

    public function removeCityBorn(Praticien $cityBorn): self
    {
        if ($this->city_born->contains($cityBorn)) {
            $this->city_born->removeElement($cityBorn);
            // set the owning side to null (unless already changed)
            if ($cityBorn->getAdressBorn() === $this) {
                $cityBorn->setAdressBorn(null);
            }
        }

        return $this;
    }
}

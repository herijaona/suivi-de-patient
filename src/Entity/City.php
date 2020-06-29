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
    private $nameCity;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="cities")
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="ville")
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity=CentreHealth::class, mappedBy="city")
     */
    private $centreHealths;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->centreHealths = new ArrayCollection();
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

}

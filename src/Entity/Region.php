<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegionRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:region"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class Region
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:state", "read:region"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:state", "read:region"})
     */
    private $nameRegion;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="regions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=City::class, mappedBy="region")
     */
    private $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameRegion(): ?string
    {
        return $this->nameRegion;
    }

    public function setNameRegion(string $nameRegion): self
    {
        $this->nameRegion = $nameRegion;

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
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setRegion($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getRegion() === $this) {
                $city->setRegion(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getNameRegion();
    }

}

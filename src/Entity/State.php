<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:state"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class State
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:state", "read:patient"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:state"})
     */
    private $nameState;

    /**
     * @ORM\OneToMany(targetEntity=Region::class, mappedBy="state", orphanRemoval=true)
     * @Groups({"read:state"})
     */
    private $regions;



    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameState(): ?string
    {
        return $this->nameState;
    }

    public function setNameState(string $name_state): self
    {
        $this->nameState = $name_state;

        return $this;
    }

    /**
     * @return Collection|Region[]
     */
    public function getRegions(): Collection
    {
        return $this->regions;
    }

    public function addRegion(Region $region): self
    {
        if (!$this->regions->contains($region)) {
            $this->regions[] = $region;
            $region->setState($this);
        }

        return $this;
    }

    public function removeRegion(Region $region): self
    {
        if ($this->regions->contains($region)) {
            $this->regions->removeElement($region);
            // set the owning side to null (unless already changed)
            if ($region->getState() === $this) {
                $region->setState(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNameState();
    }


}

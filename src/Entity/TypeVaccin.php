<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\TypeVaccinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeVaccinRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:typevaccin"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class TypeVaccin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:typevaccin", "read:vaccin"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:typevaccin", "read:vaccin"})
     */
    private $typeName;

    /**
     * @ORM\OneToMany(targetEntity=Vaccin::class, mappedBy="Type_vaccin")
     */
    private $vaccins;

    public function __construct()
    {
        $this->vaccins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(string $type_name): self
    {
        $this->typeName = $type_name;

        return $this;
    }

    /**
     * @return Collection|Vaccin[]
     */
    public function getVaccins(): Collection
    {
        return $this->vaccins;
    }

    public function addVaccin(Vaccin $vaccin): self
    {
        if (!$this->vaccins->contains($vaccin)) {
            $this->vaccins[] = $vaccin;
            $vaccin->setTypeVaccin($this);
        }

        return $this;
    }

    public function removeVaccin(Vaccin $vaccin): self
    {
        if ($this->vaccins->contains($vaccin)) {
            $this->vaccins->removeElement($vaccin);
            // set the owning side to null (unless already changed)
            if ($vaccin->getTypeVaccin() === $this) {
                $vaccin->setTypeVaccin(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getTypeName();
    }

}

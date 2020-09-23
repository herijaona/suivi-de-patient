<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FonctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=FonctionRepository::class)
 */
class Fonction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fonction;

    /**
     * @ORM\OneToMany(targetEntity=Praticien::class, mappedBy="fonction")
     */
    private $praticiens;

    public function __construct()
    {
        $this->praticiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $praticien->setFonction($this);
        }

        return $this;
    }

    public function removePraticien(Praticien $praticien): self
    {
        if ($this->praticiens->contains($praticien)) {
            $this->praticiens->removeElement($praticien);
            // set the owning side to null (unless already changed)
            if ($praticien->getFonction() === $this) {
                $praticien->setFonction(null);
            }
        }

        return $this;
    }
}

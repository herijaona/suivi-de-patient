<?php

namespace App\Entity;

use App\Repository\SpecialiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecialiteRepository::class)
 */
class Specialite
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
    private $nomSpecialite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $noteSpecialite;

    /**
     * @ORM\OneToMany(targetEntity=PraticienSpecialite::class, mappedBy="specialite")
     */
    private $praticienSpecialites;

    public function __construct()
    {
        $this->praticienSpecialites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSpecialite(): ?string
    {
        return $this->nomSpecialite;
    }

    public function setNomSpecialite(string $nomSpecialite): self
    {
        $this->nomSpecialite = $nomSpecialite;

        return $this;
    }

    public function getNoteSpecialite(): ?string
    {
        return $this->noteSpecialite;
    }

    public function setNoteSpecialite(?string $noteSpecialite): self
    {
        $this->noteSpecialite = $noteSpecialite;

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
            $praticienSpecialite->setSpecialite($this);
        }

        return $this;
    }

    public function removePraticienSpecialite(PraticienSpecialite $praticienSpecialite): self
    {
        if ($this->praticienSpecialites->contains($praticienSpecialite)) {
            $this->praticienSpecialites->removeElement($praticienSpecialite);
            // set the owning side to null (unless already changed)
            if ($praticienSpecialite->getSpecialite() === $this) {
                $praticienSpecialite->setSpecialite(null);
            }
        }

        return $this;
    }
}

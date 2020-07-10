<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PraticienSpecialiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PraticienSpecialiteRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:PraticienSpecialite"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class PraticienSpecialite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:PraticienSpecialite", "read:praticien", "read:specialite"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="praticienSpecialites")
     * @Groups({"read:PraticienSpecialite", "read:praticien"})
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(targetEntity=Specialite::class, inversedBy="praticienSpecialites")
     * @Groups({"read:PraticienSpecialite", "read:praticien"})
     */
    private $specialite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPraticien(): ?Praticien
    {
        return $this->praticien;
    }

    public function setPraticien(?Praticien $praticien): self
    {
        $this->praticien = $praticien;

        return $this;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialite $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }
}

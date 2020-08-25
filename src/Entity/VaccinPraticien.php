<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\VaccinPraticienRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VaccinPraticienRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:VaccinPraticien"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class VaccinPraticien
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:VaccinPraticien"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="vaccinPraticiens")
     * @Groups({"read:VaccinPraticien"})
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="vaccinPraticiens")
     * @Groups({"read:VaccinPraticien"})
     */
    private $vaccin;

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

    public function getVaccin(): ?Vaccin
    {
        return $this->vaccin;
    }

    public function setVaccin(?Vaccin $vaccin): self
    {
        $this->vaccin = $vaccin;

        return $this;
    }
}

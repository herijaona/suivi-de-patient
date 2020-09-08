<?php

namespace App\Entity;

use App\Repository\AssocierRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssocierRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:associer"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class Associer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:patient", "read:associer"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="associers")
     * @Groups({"read:associer"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Praticien::class, inversedBy="associers")
     * @Groups({"read:associer"})
     */
    private $praticien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
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
}

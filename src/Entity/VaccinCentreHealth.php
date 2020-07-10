<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\VaccinCentreHealthRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VaccinCentreHealthRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"read:center"}},
 *    collectionOperations={"get"},
 *    itemOperations={"get"}
 * )
 */
class VaccinCentreHealth
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:center"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CentreHealth::class, inversedBy="vaccinCentreHealths")
     * @Groups({"read:center"})
     */
    private $centreHealth;

    /**
     * @ORM\ManyToOne(targetEntity=Vaccin::class, inversedBy="vaccinCentreHealths")
     * @Groups({"read:center"})
     */
    private $vaccin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCentreHealth(): ?CentreHealth
    {
        return $this->centreHealth;
    }

    public function setCentreHealth(?CentreHealth $centreHealth): self
    {
        $this->centreHealth = $centreHealth;

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

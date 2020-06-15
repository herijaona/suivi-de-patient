<?php

namespace App\Entity;

use App\Repository\PraticienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PraticienRepository::class)
 */
class Praticien
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
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone_professional;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonction;

    /**
     * @ORM\Column(type="date")
     */
    private $date_born;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="praticiens")
     */
    private $adress;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="city_born")
     */
    private $adress_born;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=RendezVous::class, mappedBy="praticien")
     */
    private $rendezVous;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        $this->rendezVous = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhoneProfessional(): ?string
    {
        return $this->phone_professional;
    }

    public function setPhoneProfessional(?string $phone_professional): self
    {
        $this->phone_professional = $phone_professional;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getDateBorn(): ?\DateTimeInterface
    {
        return $this->date_born;
    }

    public function setDateBorn(\DateTimeInterface $date_born): self
    {
        $this->date_born = $date_born;

        return $this;
    }

    public function getAdress(): ?City
    {
        return $this->adress;
    }

    public function setAdress(?City $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getAdressBorn(): ?City
    {
        return $this->adress_born;
    }

    public function setAdressBorn(?City $adress_born): self
    {
        $this->adress_born = $adress_born;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|RendezVous[]
     */
    public function getRendezVous(): Collection
    {
        return $this->rendezVous;
    }

    public function addRendezVou(RendezVous $rendezVou): self
    {
        if (!$this->rendezVous->contains($rendezVou)) {
            $this->rendezVous[] = $rendezVou;
            $rendezVou->setPraticien($this);
        }

        return $this;
    }

    public function removeRendezVou(RendezVous $rendezVou): self
    {
        if ($this->rendezVous->contains($rendezVou)) {
            $this->rendezVous->removeElement($rendezVou);
            // set the owning side to null (unless already changed)
            if ($rendezVou->getPraticien() === $this) {
                $rendezVou->setPraticien(null);
            }
        }

        return $this;
    }

}

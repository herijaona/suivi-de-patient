<?php

namespace App\Entity;

use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get",
 *          "post"={
 *            "controller"=App\Controller\Api\InscriptionUserController::class
 *         }
 *      },
 *     itemOperations={
 *        "get", 
 *        "put", 
 *        "delete"
 *     },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}}
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *  @Groups("user:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups("user:read")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("user:read")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups("user:read")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups("user:read")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
     */
    private $firstName;

    /**
     * @ORM\Column(type="integer")
     * @Groups("user:read")
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activatorId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
        $this->roles[] = 'ROLE_PATIENT';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function setUsername(string  $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_PATIENT';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $last_name): self
    {
        $this->lastName = $last_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $first_name): self
    {
        $this->firstName = $first_name;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getActivatorId(): ?string
    {
        return $this->activatorId;
    }

    public function setActivatorId(?string $activator_id): self
    {
        $this->activatorId = $activator_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

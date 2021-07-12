<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CharacterRepository::class)
 * @ORM\Table(name="`character`")
 */
class Character
{
    const FACTION_HORDE = 'Horde';
    const FACTION_ALLIANCE = 'Alliance';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(
     *     message = "You must specify a character name"
     * )
     * @Assert\Length(
     *     max = 250,
     *     maxMessage = "Your character name cannot be longer than 250 characters"
     * ) 
     * @Assert\Regex(
     *     pattern = "/^\w{1,}$/",
     *     message = "Your character name cannot contain space or special character (except underscore)"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $information;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchived;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="characters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Assert\NotNull(
     *     message = "You must specify a character class"
     * )
     *  
     * @ORM\ManyToOne(targetEntity=CharacterClass::class, inversedBy="characters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $characterClass;

    /**
     * @Assert\NotNull(
     *     message = "You must specify a server"
     * )
     *  
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="characters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $server;

    /**
     * @ORM\ManyToOne(targetEntity=Faction::class, inversedBy="characters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $faction;

    /**
     * @Assert\Count(
     *     min = 1,
     *     minMessage = "You must specify at least one role"
     * )
     * 
     * @ORM\ManyToMany(targetEntity=Role::class, inversedBy="characters")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=RaidCharacter::class, mappedBy="userCharacter", orphanRemoval=true)
     */
    private $raidCharacters;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->roles = new ArrayCollection();
        $this->raidCharacters = new ArrayCollection();
    }

    function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
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

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

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

    public function getCharacterClass(): ?CharacterClass
    {
        return $this->characterClass;
    }

    public function setCharacterClass(?CharacterClass $characterClass): self
    {
        $this->characterClass = $characterClass;

        return $this;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    public function setFaction(?Faction $faction): self
    {
        $this->faction = $faction;

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return Collection|RaidCharacter[]
     */
    public function getRaidCharacters(): Collection
    {
        return $this->raidCharacters;
    }

    public function addRaidCharacter(RaidCharacter $raidCharacter): self
    {
        if (!$this->raidCharacters->contains($raidCharacter)) {
            $this->raidCharacters[] = $raidCharacter;
            $raidCharacter->setUserCharacter($this);
        }

        return $this;
    }

    public function removeRaidCharacter(RaidCharacter $raidCharacter): self
    {
        if ($this->raidCharacters->removeElement($raidCharacter)) {
            // set the owning side to null (unless already changed)
            if ($raidCharacter->getUserCharacter() === $this) {
                $raidCharacter->setUserCharacter(null);
            }
        }

        return $this;
    }
}

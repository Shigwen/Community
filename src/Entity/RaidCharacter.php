<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RaidCharacterRepository;

/**
 * @ORM\Entity(repositoryClass=RaidCharacterRepository::class)
 */
class RaidCharacter
{
    const WAITING_CONFIRMATION = 0;
    const ACCEPT = 1;
    const REFUSED = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Raid::class, inversedBy="raidCharacters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $raid;

    /**
     * @ORM\ManyToOne(targetEntity=Character::class, inversedBy="raidCharacters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCharacter;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="raidCharacters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaid(): ?Raid
    {
        return $this->raid;
    }

    public function setRaid(?Raid $raid): self
    {
        $this->raid = $raid;

        return $this;
    }

    public function getUserCharacter(): ?Character
    {
        return $this->userCharacter;
    }

    public function setUserCharacter(?Character $userCharacter): self
    {
        $this->userCharacter = $userCharacter;

        return $this;
    }

    public function isRaidLeaderCharacter(User $user)
    {
        if ($this->userCharacter->getUser() !== $user) {
            return false;
        }

        return true;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getCharacterServer()
    {
        if (!$this->userCharacter) {
            return null;
        }

        return $this->userCharacter->getServer();
    }

    public function getUser()
    {
        if (!$this->userCharacter) {
            return null;
        }

        return $this->userCharacter->getUser();
    }

    public function isAccept(): ?int
    {
        return $this->status === $this::ACCEPT;
    }

    public function isWaitingConfirmation(): ?int
    {
        return $this->status === $this::WAITING_CONFIRMATION;
    }

    public function isRefused(): ?int
    {
        return $this->status === $this::REFUSED;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}

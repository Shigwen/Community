<?php

namespace App\Entity;

use DateTime;
use App\Entity\RaidTemplate;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
	const DELAY_AFTER_MAX_ATTEMPT = 5;
	const NUMBER_MAX_OF_ATTEMPT = 5;

	const STATUS_WAITING_EMAIL_CONFIRMATION = 0;
	const STATUS_EMAIL_CONFIRMED = 1;
	const STATUS_BAN = 2;

	const ROLE_USER = 'ROLE_USER';
	const ROLE_RAID_LEADER = 'ROLE_RAID_LEADER';
	const ROLE_ADMIN = 'ROLE_ADMIN';
	const ROLE_OWNER = 'ROLE_OWNER';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

	/**
     * @ORM\Column(type="smallint")
     */
    private $status;

	/**
     * @ORM\Column(type="smallint")
     */
    private $nbrOfAttempt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastAttempt;

	/**
     * @var Date
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

	/**
     * @var Date
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=RaidTemplate::class, mappedBy="user", orphanRemoval=true)
     */
    private $raidTemplates;

    /**
     * @ORM\OneToMany(targetEntity=Raid::class, mappedBy="user", orphanRemoval=true)
     */
    private $raids;

    /**
     * @ORM\OneToMany(targetEntity=Ip::class, mappedBy="user", orphanRemoval=true)
     */
    private $ips;

    /**
     * List of users blocked by the raid leader
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="blockers")
     */
    private $blockeds;

    /**
     * List of raid leader who blocked the user
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="blockeds")
     */
    private $blockers;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="user", orphanRemoval=true)
     */
    private $characters;

    public function __construct()
    {
		$this->createdAt = new DateTime();
        $this->raidTemplates = new ArrayCollection();
        $this->raids = new ArrayCollection();
        $this->ips = new ArrayCollection();
        $this->blockeds = new ArrayCollection();
        $this->blockers = new ArrayCollection();
        $this->characters = new ArrayCollection();
    }

	public function __toString()
	{
		return $this->email;
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
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
		if (empty($roles = $this->roles)) {
			$roles[] = 'ROLE_USER';
		}

        return array_unique($roles);
    }

	/**
     * @see UserInterface
     */
    public function getStrRole(): string
    {
        return $this->roles[0];
    }

	/**
	 * Get the verbose name of user's first role
     * @return string
     */
    public function getVerboseStrRole(): string
    {
		switch($this->roles[0]) {
			case 'ROLE_USER':
				$role = 'User';
				break;
			case 'ROLE_RAID_LEADER':
				$role = 'Raid Leader';
				break;
			case 'ROLE_ADMIN':
				$role = 'Administrator';
				break;
			case 'ROLE_OWNER':
				$role = 'Owner';
				break;
		}

        return  $role;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

	public function getNbrOfAttempt(): ?int
    {
        return $this->nbrOfAttempt;
    }

    public function setNbrOfAttempt(int $nbrOfAttempt): self
    {
        $this->nbrOfAttempt = $nbrOfAttempt;

        return $this;
    }

    public function getLastAttempt(): ?\DateTimeInterface
    {
        return $this->lastAttempt;
    }

    public function setLastAttempt(\DateTimeInterface $lastAttempt): self
    {
        $this->lastAttempt = $lastAttempt;

        return $this;
    }

	/**
     * @return  \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return  \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param  \DateTime  $updatedAt
     * @return  self
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|RaidTemplate[]
     */
    public function getRaidTemplates(): Collection
    {
        return $this->raidTemplates;
    }

    public function addRaidTemplate(RaidTemplate $raidTemplate): self
    {
        if (!$this->raidTemplates->contains($raidTemplate)) {
            $this->raidTemplates[] = $raidTemplate;
            $raidTemplate->setUser($this);
        }

        return $this;
    }

    public function removeRaidTemplate(RaidTemplate $raidTemplate): self
    {
        if ($this->raidTemplates->removeElement($raidTemplate)) {
            // set the owning side to null (unless already changed)
            if ($raidTemplate->getUser() === $this) {
                $raidTemplate->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Raid[]
     */
    public function getRaids(): Collection
    {
        return $this->raids;
    }

    public function addRaid(Raid $raid): self
    {
        if (!$this->raids->contains($raid)) {
            $this->raids[] = $raid;
            $raid->setUser($this);
        }

        return $this;
    }

    public function removeRaid(Raid $raid): self
    {
        if ($this->raids->removeElement($raid)) {
            // set the owning side to null (unless already changed)
            if ($raid->getUser() === $this) {
                $raid->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ip[]
     */
    public function getIps(): Collection
    {
        return $this->ips;
    }

    public function addIp(Ip $ip): self
    {
        if (!$this->ips->contains($ip)) {
            $this->ips[] = $ip;
            $ip->setUser($this);
        }

        return $this;
    }

    public function removeIp(Ip $ip): self
    {
        if ($this->ips->removeElement($ip)) {
            // set the owning side to null (unless already changed)
            if ($ip->getUser() === $this) {
                $ip->setUser(null);
            }
        }

        return $this;
    }

    public function getBlockeds(): Collection
    {
        return $this->blockeds;
    }

    public function hasBlocked(self $blockedToSearch)
    {
        foreach ($this->blockeds as $blocked) {
            if ($blocked === $blockedToSearch) {
                return $blocked;
            }
        }

        return null;
    }

    public function addBlocked(self $blocked): self
    {
        if (!$this->blockeds->contains($blocked)) {
            $this->blockeds[] = $blocked;
        }

        return $this;
    }

    public function removeBlocked(self $blocked): self
    {
        $this->blockeds->removeElement($blocked);

        return $this;
    }

    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setUser($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getUser() === $this) {
                $character->setUser(null);
            }
        }

        return $this;
    }

	public function hasCharacter(Character $character) : bool
	{
		if ($this->characters->contains($character)) {
			return true;
		}

		return false;
	}

	public function hasRaid(Raid $raid) : bool
	{
		if ($this->raids->contains($raid)) {
			return true;
		}

		return false;
	}

    public function hasRaidTemplate(RaidTemplate $raidTemplate) : bool
	{
		if ($this->raidTemplates->contains($raidTemplate)) {
			return true;
		}

		return false;
	}
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @var Date
     * @ORM\Column(type="date")
     */
    private $createdAt;

	/**
     * @var Date
     * @ORM\Column(type="date", nullable=true)
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
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="blockers")
     */
    private $blockeds;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="blockeds")
     */
    private $blockers;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="user", orphanRemoval=true)
     */
    private $characters;

    public function __construct()
    {
        $this->raidTemplates = new ArrayCollection();
        $this->raids = new ArrayCollection();
        $this->ips = new ArrayCollection();
        $this->blockeds = new ArrayCollection();
        $this->blockers = new ArrayCollection();
        $this->characters = new ArrayCollection();
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

	/**
     * @return  \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param  \DateTime  $createdAt
     * @return  self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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

    /**
     * @return Collection|self[]
     */
    public function getBlockeds(): Collection
    {
        return $this->blockeds;
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

    /**
     * @return Collection|self[]
     */
    public function getBlockers(): Collection
    {
        return $this->blockers;
    }

    public function addBlocker(self $blocker): self
    {
        if (!$this->blockers->contains($blocker)) {
            $this->blockers[] = $blocker;
            $blocker->addBlocked($this);
        }

        return $this;
    }

    public function removeBlocker(self $blocker): self
    {
        if ($this->blockers->removeElement($blocker)) {
            $blocker->removeBlocked($this);
        }

        return $this;
    }

    /**
     * @return Collection|Character[]
     */
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
}

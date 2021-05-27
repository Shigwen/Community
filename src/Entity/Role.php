<?php

namespace App\Entity;

use DateTime;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RoleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
{
	const TANK = 1;
	const HEAL = 2;
	const DPS = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Character::class, mappedBy="roles")
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity=RaidCharacter::class, mappedBy="role", orphanRemoval=true)
     */
    private $raidCharacters;

    public function __construct()
    {
		$this->createdAt = new DateTime();
        $this->characters = new ArrayCollection();
        $this->raidCharacters = new ArrayCollection();
    }

	public function __toString()
	{
		return $this->name;
	}

	public function validate(ArrayCollection $roles, ExecutionContextInterface $context)
    {
		foreach ($roles as $role) {
			if (in_array($role->getId(), [self::TANK, self::HEAL, self::DPS])) {
				return true;
			}
		}

		$context->buildViolation('You must choose at least one role')
			->atPath('name')
			->addViolation();
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
            $character->addRole($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->removeElement($character)) {
            $character->removeRole($this);
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
            $raidCharacter->setRole($this);
        }

        return $this;
    }

    public function removeRaidCharacter(RaidCharacter $raidCharacter): self
    {
        if ($this->raidCharacters->removeElement($raidCharacter)) {
            // set the owning side to null (unless already changed)
            if ($raidCharacter->getRole() === $this) {
                $raidCharacter->setRole(null);
            }
        }

        return $this;
    }

	public function isTank()
	{
		return $this->id === self::TANK;
	}

	public function isHeal()
	{
		return $this->id === self::HEAL;
	}
}

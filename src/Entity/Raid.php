<?php

namespace App\Entity;

use App\Repository\RaidRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RaidRepository::class)
 */
class Raid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $raid_type;

    /**
     * @ORM\Column(type="smallint")
     */
    private $expected_attendee;

	/**
     * @ORM\Column(type="datetime")
     */
    private $start_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end_at;

    /**
     * @ORM\Column(type="text")
     */
    private $information;

    /**
     * @ORM\Column(type="smallint")
     */
    private $min_tank;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $max_tank;

    /**
     * @ORM\Column(type="smallint")
     */
    private $min_heal;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $max_heal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="raids")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=RaidCharacter::class, mappedBy="raid", orphanRemoval=true)
     */
    private $raidCharacters;

    public function __construct()
    {
        $this->raidCharacters = new ArrayCollection();
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

    public function getRaidType(): ?int
    {
        return $this->raid_type;
    }

    public function setRaidType(int $raid_type): self
    {
        $this->raid_type = $raid_type;

        return $this;
    }

    public function getExpectedAttendee(): ?int
    {
        return $this->expected_attendee;
    }

    public function setExpectedAttendee(int $expected_attendee): self
    {
        $this->expected_attendee = $expected_attendee;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeInterface $end_at): self
    {
        $this->end_at = $end_at;

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

    public function getMinTank(): ?int
    {
        return $this->min_tank;
    }

    public function setMinTank(int $min_tank): self
    {
        $this->min_tank = $min_tank;

        return $this;
    }

    public function getMaxTank(): ?int
    {
        return $this->max_tank;
    }

    public function setMaxTank(?int $max_tank): self
    {
        $this->max_tank = $max_tank;

        return $this;
    }

    public function getMinHeal(): ?int
    {
        return $this->min_heal;
    }

    public function setMinHeal(int $min_heal): self
    {
        $this->min_heal = $min_heal;

        return $this;
    }

    public function getMaxHeal(): ?int
    {
        return $this->max_heal;
    }

    public function setMaxHeal(?int $max_heal): self
    {
        $this->max_heal = $max_heal;

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
            $raidCharacter->setRaid($this);
        }

        return $this;
    }

    public function removeRaidCharacter(RaidCharacter $raidCharacter): self
    {
        if ($this->raidCharacters->removeElement($raidCharacter)) {
            // set the owning side to null (unless already changed)
            if ($raidCharacter->getRaid() === $this) {
                $raidCharacter->setRaid(null);
            }
        }

        return $this;
    }
}

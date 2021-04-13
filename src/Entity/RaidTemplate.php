<?php

namespace App\Entity;

use App\Repository\RaidTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RaidTemplateRepository::class)
 */
class RaidTemplate
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
     * @ORM\Column(type="smallint")
     */
    private $day_of_week;

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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="raidTemplates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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

    public function getDayOfWeek(): ?int
    {
        return $this->day_of_week;
    }

    public function setDayOfWeek(int $day_of_week): self
    {
        $this->day_of_week = $day_of_week;

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
}

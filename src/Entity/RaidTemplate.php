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
    private $raidType;

    /**
     * @ORM\Column(type="smallint")
     */
    private $expectedAttendee;

    /**
     * @ORM\Column(type="smallint")
     */
    private $dayOfWeek;

	/**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    /**
     * @ORM\Column(type="text")
     */
    private $information;

    /**
     * @ORM\Column(type="smallint")
     */
    private $minTank;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $maxTank;

    /**
     * @ORM\Column(type="smallint")
     */
    private $minHeal;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $maxHeal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

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
        return $this->raidType;
    }

    public function setRaidType(int $raidType): self
    {
        $this->raidType = $raidType;

        return $this;
    }

    public function getExpectedAttendee(): ?int
    {
        return $this->expectedAttendee;
    }

    public function setExpectedAttendee(int $expectedAttendee): self
    {
        $this->expectedAttendee = $expectedAttendee;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

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
        return $this->minTank;
    }

    public function setMinTank(int $minTank): self
    {
        $this->minTank = $minTank;

        return $this;
    }

    public function getMaxTank(): ?int
    {
        return $this->maxTank;
    }

    public function setMaxTank(?int $maxTank): self
    {
        $this->maxTank = $maxTank;

        return $this;
    }

    public function getMinHeal(): ?int
    {
        return $this->minHeal;
    }

    public function setMinHeal(int $minHeal): self
    {
        $this->minHeal = $minHeal;

        return $this;
    }

    public function getMaxHeal(): ?int
    {
        return $this->maxHeal;
    }

    public function setMaxHeal(?int $maxHeal): self
    {
        $this->maxHeal = $maxHeal;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
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

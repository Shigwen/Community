<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RaidRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AssertCustom;

/**
 * @ORM\Entity(repositoryClass=RaidRepository::class)
 */
class Raid
{
    const IDENTIFIER_SIZE = 20;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $identifier;

    /**
     * @Assert\NotBlank(
     *     message = "You must specify a raid name"
     * )
     * @Assert\Length(
     *     max = 250,
     *     maxMessage = "The raid name cannot be longer than 250 characters"
     * )
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\Length(
     *     max = 250,
     *     maxMessage = "The template name cannot be longer than 250 characters"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $templateName;

    /**
     * @Assert\NotNull(
     *     message = "You must specify a raid type"
     * )
     * @Assert\Choice(
     *     choices = {10, 25, 40},
     *     message = "Choose a valid raid type"
     * )
     * 
     * @ORM\Column(type="smallint")
     */
    private $raidType;

    /**
     * @Assert\NotBlank(
     *     message = "The number of people you are looking for cannot be blank"
     * )
     * @Assert\Positive(
     *     message = "Cannot use negative value"
     * )
     * @Assert\LessThan(
     *     propertyPath = "raidType",
     *     message = "The number of people you are looking for must be inferior to the size of the raid"
     * )
     * @AssertCustom\GreaterThanMaxTankAndHeal()
     * 
     * @ORM\Column(type="smallint")
     */
    private $expectedAttendee;

    /**
     * @Assert\GreaterThan(
     *     value = "+ 1 hour",
     *     message = "The date must be in the future"
     * )
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @AssertCustom\GreaterThanStartAt()
     * 
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    /**
     * @ORM\Column(type="text")
     */
    private $information;

    /**
     * @Assert\Positive(
     *     message = "Cannot use negative value"
     * )
     *  
     * @ORM\Column(type="smallint")
     */
    private $minTank;

    /**
     * @Assert\NotBlank(
     *     message = "The maximum number of tanks you are looking for cannot be blank"
     * ) 
     * @Assert\GreaterThan(
     *     propertyPath = "minTank",
     *     message = "The maximum number of tanks cannot be inferior to the minimum"
     * )
     *  
     * @ORM\Column(type="smallint")
     */
    private $maxTank;

    /**
     * @Assert\Positive(
     *     message = "Cannot use negative value"
     * )
     *  
     * @ORM\Column(type="smallint")
     */
    private $minHeal;

    /**
     * @Assert\NotBlank(
     *     message = "The maximum number of healers you are looking for cannot be blank"
     * ) 
     * @Assert\GreaterThan(
     *     propertyPath = "minHeal",
     *     message = "The maximum number of healers cannot be inferior to the minimum"
     * )
     *  
     * @ORM\Column(type="smallint")
     */
    private $maxHeal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $autoAccept;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="raids")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=RaidCharacter::class, mappedBy="raid", cascade={"persist"}, orphanRemoval=true)
     */
    private $raidCharacters;

    /**
     * @ORM\ManyToOne(targetEntity=Server::class, inversedBy="raids")
     * @ORM\JoinColumn(nullable=true)
     */
    private $server;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchived;

    public function __construct()
    {
        $this->startAt = new DateTime();
        $this->endAt = new DateTime();
        $this->createdAt = new DateTime();
        $this->raidCharacters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier): self
    {
        $this->identifier = $identifier;

        return $this;
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

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function setTemplateName($templateName): self
    {
        $this->templateName = $templateName;

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

    public function getVerboseStartDayOfWeek()
    {
        if (!$this->getStartAt()) {
            return null;
        }

        return [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ][$this->getStartAt()->format('N')]; // N format return 1 (for monday) to 7 (for sunday)
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

    public function getAutoAccept(): ?bool
    {
        return $this->autoAccept;
    }

    public function setAutoAccept(bool $autoAccept): self
    {
        $this->autoAccept = $autoAccept;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRaidCharacters(): Collection
    {
        return $this->raidCharacters;
    }

    public function getRaidCharacterFromUser(User $user): RaidCharacter
    {
        foreach ($this->raidCharacters as $raidCharacter) {
            if ($raidCharacter->getUser() === $user) {
                return $raidCharacter;
            }
        }
        return null;
    }

    public function getCharacterFromUser(User $user): Character
    {
        foreach ($this->raidCharacters as $raidCharacter) {
            if ($raidCharacter->getUser() === $user) {
                return $raidCharacter->getUserCharacter();
            }
        }
        return null;
    }

    public function hasCharacter(Character $character)
    {
        foreach ($this->raidCharacters as $raidCharacter) {
            if ($raidCharacter->getUserCharacter = $character) {
                return true;
            }
        }
        return false;
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

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }
}

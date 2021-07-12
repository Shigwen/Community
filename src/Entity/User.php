<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * 
 *  @UniqueEntity(
 *     fields={"email"}, 
 *     message="Email already used"
 * )
 *  @UniqueEntity(
 *     fields={"name"}, 
 *     message="Nickname already used"
 * )
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
     * @Assert\NotBlank(
     *     message = "You must specify a username"
     * )
     * @Assert\Length(
     *     max = 250,
     *     maxMessage = "Your username cannot be longer than 250 characters"
     * )
     * @Assert\Regex(
     *     pattern = "/^\w{1,}$/",
     *     message = "Your username cannot contain space or special character (except underscore)"
     * )
     *
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @Assert\NotBlank(
     *     message = "You must specify an email"
     * )
     * @Assert\Email(
     *     message = "This email is not valid"
     * )
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Timezone::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $timezone;

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
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

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
     * @ORM\OneToMany(targetEntity=Raid::class, mappedBy="user", orphanRemoval=true)
     */
    private $raids;

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
        $this->roles = [self::ROLE_RAID_LEADER];
        $this->status = self::STATUS_WAITING_EMAIL_CONFIRMATION;
        $this->nbrOfAttempt = 0;
        $this->lastAttempt = new DateTime();

        $this->createdAt = new DateTime();
        $this->raids = new ArrayCollection();
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUsername()
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        if (empty($roles = $this->roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function getStrRole()
    {
        return $this->roles[0];
    }

    /**
     * Get the verbose name of user's first role
     * @return string
     */
    public function getVerboseStrRole()
    {
        switch ($this->roles[0]) {
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

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return (string) $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getTimezone(): ?Timezone
    {
        return $this->timezone;
    }

    public function setTimezone(?Timezone $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getNbrOfAttempt()
    {
        return $this->nbrOfAttempt;
    }

    public function setNbrOfAttempt($nbrOfAttempt)
    {
        $this->nbrOfAttempt = $nbrOfAttempt;

        return $this;
    }

    public function getLastAttempt()
    {
        return $this->lastAttempt;
    }

    public function setLastAttempt($lastAttempt)
    {
        $this->lastAttempt = $lastAttempt;

        return $this;
    }

    public function getToken()
    {
        return (string) $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

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
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
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
     * @return Collection|Raid[]
     */
    public function getRaids()
    {
        return $this->raids;
    }

    public function addRaid($raid)
    {
        if (!$this->raids->contains($raid)) {
            $this->raids[] = $raid;
            $raid->setUser($this);
        }

        return $this;
    }

    public function removeRaid($raid)
    {
        if ($this->raids->removeElement($raid)) {
            // set the owning side to null (unless already changed)
            if ($raid->getUser() === $this) {
                $raid->setUser(null);
            }
        }

        return $this;
    }

    public function getBlockeds()
    {
        return $this->blockeds;
    }

    public function hasBlocked($blockedToSearch)
    {
        foreach ($this->blockeds as $blocked) {
            if ($blocked === $blockedToSearch) {
                return $blocked;
            }
        }

        return null;
    }

    public function addBlocked($blocked)
    {
        if (!$this->blockeds->contains($blocked)) {
            $this->blockeds[] = $blocked;
        }

        return $this;
    }

    public function removeBlocked($blocked)
    {
        $this->blockeds->removeElement($blocked);

        return $this;
    }

    public function getStrCharacterList()
    {
        $str = '';
        foreach ($this->characters as $character) {
            $str .= $character->getName() . ', ';
        }

        $str = substr($str, 0, -2);

        return $str;
    }

    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter($character)
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setUser($this);
        }

        return $this;
    }

    public function removeCharacter($character)
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getUser() === $this) {
                $character->setUser(null);
            }
        }

        return $this;
    }

    public function hasCharacter($character)
    {
        if ($this->characters->contains($character)) {
            return true;
        }

        return false;
    }

    public function hasRaid($raid)
    {
        if ($this->raids->contains($raid)) {
            return true;
        }

        return false;
    }
}

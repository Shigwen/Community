<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ServerRepository::class)
 */
class Server
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
     * @ORM\ManyToOne(targetEntity=GameVersion::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVersion;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity=Timezone::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $timezone;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="server", orphanRemoval=true)
     */
    private $characters;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->characters = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameVersion(): ?GameVersion
    {
        return $this->gameVersion;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function getVerboseVersionAndRegion()
    {
        return $this->gameVersion->getName() . ' - ' . $this->getRegion()->getName();
    }
    
    public function getVerboseVersionAndName()
    {
        return $this->gameVersion->getName() . ' - ' . $this->name;
    }

    public function getTimezone(): ?Timezone
    {
        return $this->timezone;
    }

    public function getName(): ?string
    {
        return $this->name;
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
            $character->setServer($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getServer() === $this) {
                $character->setServer(null);
            }
        }

        return $this;
    }

    public function removeRaid(Raid $raid): self
    {
        if ($this->raids->removeElement($raid)) {
            // set the owning side to null (unless already changed)
            if ($raid->getServer() === $this) {
                $raid->setServer(null);
            }
        }

        return $this;
    }
}

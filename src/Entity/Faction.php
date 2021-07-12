<?php

namespace App\Entity;

use App\Repository\FactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactionRepository::class)
 */
class Faction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="faction")
     */
    private $characters;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageSource(): ?string
    {
        return "https://wow.zamimg.com/images/wow/icons/large/ui_" . strtolower($this->name) . "icon.jpg";
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
            $character->setFaction($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getFaction() === $this) {
                $character->setFaction(null);
            }
        }

        return $this;
    }
}

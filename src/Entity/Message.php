<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
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
     * @Assert\Email(
     *     message = "This email is not valid"
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=MessageType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $messageType;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $archivedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $archivedBy;

    public function __construct()
    {
        $this->archivedBy = new ArrayCollection();
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

    public function getMessageType(): ?MessageType
    {
        return $this->messageType;
    }

    public function setMessageType(?MessageType $messageType): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    public function getArchivedBy()
    {
        return $this->archivedBy;
    }

    public function setArchivedBy(?User $archivedBy): self
    {
        $this->archivedBy = $archivedBy;

        return $this;
    }
}

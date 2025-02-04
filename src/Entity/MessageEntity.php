<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MessageEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Uid\Uuid;
use App\Entity\User;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: MessageEntityRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put()
    ]
)]
class MessageEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $sendAt = null;

    #[ORM\Column(type: 'uuid')]
    private ?User $sender = null;

    #[ORM\Column(type: 'uuid')]
    private ?User $receiver = null;

    #[ORM\ManyToOne(inversedBy: 'exchangeMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParentEntity $parentHandleMessage = null;


    public function __construct()
    {

        $this->sendAt = new \DateTimeInterface();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function sendAt(): ?DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(DateTimeInterface $sendAt): static
    {
        $this->sendAt = $sendAt;
        return $this;
    }

    public function getSender(): ?Uuid
    {
        return $this->sender;
    }

    public function setSender(User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }


    public function getreceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(User $receiver): static
    {
        $this->sender = $receiver;

        return $this;
    }

    public function getParentHandleMessage(): ?ParentEntity
    {
        return $this->parentHandleMessage;
    }

    public function setParentHandleMessage(?ParentEntity $parentHandleMessage): static
    {
        $this->parentHandleMessage = $parentHandleMessage;

        return $this;
    }
}

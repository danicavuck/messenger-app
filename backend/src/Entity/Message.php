<?php

namespace App\Entity;

use App\Enum\MessageStatus;
use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  private ?string $id = null;

  #[ORM\Column(type: 'text')]
  #[Assert\NotBlank]
  private string $content;

  #[ORM\ManyToOne(targetEntity: User::class)]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  private User $user;

  #[ORM\Column(type: 'datetime_immutable')]
  private \DateTimeImmutable $createdAt;

  #[ORM\Column(type: 'string', length: 100, enumType: MessageStatus::class)]
  #[Assert\NotBlank]
  private MessageStatus $status;

  public function __construct() {
    $this->createdAt = new \DateTimeImmutable();
    $this->status = MessageStatus::SENT;
  }

  public function getId(): ?string {
    return $this->id;
  }

  public function getContent(): string {
    return $this->content;
  }

  public function setContent(string $content): self {
    $this->content = $content;
    return $this;
  }

  public function getUser(): User {
    return $this->user;
  }

  public function setUser(User $u): self {
    $this->user = $u;
    return $this;
  }

  public function getCreatedAt(): \DateTimeImmutable {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): void {
    $this->createdAt = $createdAt;
  }

  public function getStatus(): MessageStatus {
    return $this->status;
  }

  public function setStatus(MessageStatus $s): self {
    $this->status = $s;
    return $this;
  }
}

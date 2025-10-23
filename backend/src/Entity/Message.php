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

  #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
  private \DateTimeImmutable $created_at;

  #[ORM\Column(type: 'string', length: 100)]
  #[Assert\NotBlank]
  private string $status;

  #[ORM\Column(type: 'boolean', options: ['default' => false])]
  private bool $isBot = false;

  public function __construct(
    ?string $content = null,
    ?User   $user = null,
    ?string $status = null
  ) {
    $this->created_at = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    $this->status = $status ?? MessageStatus::SENT->value;
    $this->isBot = false;

    if ($content !== null) {
      $this->content = $content;
    }

    if ($user !== null) {
      $this->user = $user;
    }
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

  public function setUser(User $user): self {
    $this->user = $user;
    return $this;
  }

  public function getCreatedAt(): \DateTimeImmutable {
    return $this->created_at;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): self {
    $this->created_at = $createdAt;
    return $this;
  }

  public function getStatus(): string {
    return $this->status;
  }

  public function setStatus(string $status): self {
    $this->status = $status;
    return $this;
  }

  public function isBot(): bool {
    return $this->isBot;
  }

  public function setIsBot(bool $isBot): self {
    $this->isBot = $isBot;
    return $this;
  }
}

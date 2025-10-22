<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  private ?string $id = null;

  #[ORM\Column(length: 180)]
  private string $email;

  #[ORM\Column(length: 180)]
  private string $username;

  #[ORM\Column(type: 'json')]
  private array $roles = [];

  #[ORM\Column]
  private string $password;

  public function getId(): ?string {
    return $this->id;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function setEmail(string $email): static {
    $this->email = $email;
    return $this;
  }

  public function getUsername(): string {
    return $this->username;
  }

  public function setUsername(string $username): void {
    $this->username = $username;
  }

  public function getUserIdentifier(): string {
    return $this->email;
  }

  public function getRoles(): array {
    // Ensure every user has ROLE_USER at minimum
    $roles = $this->roles;
    if (!in_array('ROLE_USER', $roles, true)) {
      $roles[] = 'ROLE_USER';
    }

    return array_unique($roles);
  }

  public function setRoles(array $roles): static {
    $this->roles = $roles;
    return $this;
  }

  public function getPassword(): string {
    return $this->password;
  }

  public function setPassword(string $password): static {
    $this->password = $password;
    return $this;
  }

  public function eraseCredentials(): void {
    // If you store any temporary sensitive data, clear it here
  }
}

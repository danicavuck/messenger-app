<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest {
  #[Assert\NotBlank]
  #[Assert\Email]
  public string $email;

  #[Assert\NotBlank]
  #[Assert\Length(min: 8, max: 64)]
  public string $password;

  #[Assert\NotBlank]
  #[Assert\Length(min: 2, max: 50)]
  public string $username;
}

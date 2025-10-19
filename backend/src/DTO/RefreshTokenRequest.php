<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RefreshTokenRequest {
  #[Assert\NotBlank(message: 'Refresh token is required.')]
  #[Assert\Type('string')]
  public string $refresh_token;
}

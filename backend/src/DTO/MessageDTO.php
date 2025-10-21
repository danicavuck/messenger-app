<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MessageDTO {

  #[Assert\type('string')]
  #[Assert\NotBlank]
  public string $content;

}

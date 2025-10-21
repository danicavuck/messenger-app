<?php

namespace App\Event;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class MessageCreatedEvent extends Event {
  public const NAME = 'message.created';

  public function __construct(
    private readonly Message $message,
    private readonly User $user
  ) {}

  public function getMessage(): Message {
    return $this->message;
  }

  public function getUser(): User {
    return $this->user;
  }
}

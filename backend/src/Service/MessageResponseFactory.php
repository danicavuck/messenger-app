<?php

namespace App\Service;

use App\Entity\Message;

final class MessageResponseFactory {

  public function make(Message $m): array {
    $u = $m->getUser();
    return [
      'id' => $m->getId(),
      'content' => $m->getContent(),
      'status' => $m->getStatus(),
      'isBot' => $m->isBot(),
      'created_at' => $m->getCreatedAt()->format(DATE_ATOM),
      'user' => $u ? [
        'id' => $u->getId(),
        'username' => $u->getUsername(),
        'email' => $u->getEmail(),
      ] : null,
    ];
  }
}

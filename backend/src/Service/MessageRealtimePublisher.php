<?php

namespace App\Service;

use App\Entity\Message;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class MessageRealtimePublisher {
  public function __construct(private HubInterface $hub) {}

  public function publish(Message $message): void
  {
    $data = [
      'id'         => $message->getId(),
      'content'    => $message->getContent(),
      'status'     => $message->getStatus()->value,
      'isBot'      => $message->isBot(),
      'created_at' => $message->getCreatedAt()->format(DATE_ATOM),
      'user'       => [
        'id' => $message->getUser()->getId(),
        'username' => $message->getUser()->getUsername(),
      ],
    ];

    $update = new Update(
      sprintf('/messages/%s', $message->getUser()->getId()),
      json_encode($data)
    );
    $this->hub->publish($update);
  }
}

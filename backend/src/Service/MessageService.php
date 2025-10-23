<?php

namespace App\Service;

use App\DTO\MessageDTO;
use App\Entity\Message;
use App\Entity\User;
use App\Enum\MessageStatus;
use App\Event\MessageCreatedEvent;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class MessageService {
  public function __construct(
    private EntityManagerInterface   $em,
    private MessageRepository        $messages,
    private EventDispatcherInterface $eventDispatcher,
    private MessageResponseFactory   $response,
    private MessageRealtimePublisher $publisher,
  ) {}

  /**
   * @param MessageDTO $dto
   * @param User $user
   * @return array
   */
  public function create(MessageDTO $dto, User $user): array {
    if ('' === trim($dto->content)) {
      throw new \InvalidArgumentException('Message content cannot be empty.');
    }

    $message = (new Message())
      ->setUser($user)
      ->setContent($dto->content)
      ->setStatus(MessageStatus::SENT->value);

    $this->em->persist($message);
    $this->em->flush();
    $this->publisher->publish($message);

    $this->eventDispatcher->dispatch(new MessageCreatedEvent($message, $user), MessageCreatedEvent::NAME);

    return $this->response->make($message);
  }

  /**
   * @param User $user
   * @param int $page
   * @param int $limit
   * @return array
   */
  public function getUserMessages(User $user, int $page = 1, int $limit = 20): array {
    $result = $this->messages->findPaginatedByUser($user, $page, $limit);
    $items = array_map(fn(Message $m) => $this->response->make($m), $result['items']);

    return [
      'data' => $items,
      'meta' => $result['meta'],
    ];
  }

}

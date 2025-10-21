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
  ) {}

  /**
   * @param MessageDTO $dto
   * @param User $user
   * @return array
   */
  public function create(MessageDTO $dto, User $user): array {
    $message = (new Message())
      ->setUser($user)
      ->setContent($dto->content)
      ->setStatus(MessageStatus::SENT);

    $this->em->persist($message);
    $this->em->flush();

    $this->eventDispatcher->dispatch(
      new MessageCreatedEvent($message, $user),
      MessageCreatedEvent::NAME
    );

    return $this->formatResponse($message);
  }

  private function formatResponse(Message $message): array {
    return [
      'id' => $message->getId(),
      'content' => $message->getContent(),
      'status' => $message->getStatus()->value,
      'username' => $message->getUser()->getUsername(),
      'created_at' => $message->getCreatedAt()->format(\DateTimeInterface::ATOM),
    ];
  }

  /**
   * @param User $user
   * @param int $page
   * @param int $limit
   * @return array
   */
  public function getUserMessages(User $user, int $page = 1, int $limit = 20): array {
    $result = $this->messages->findPaginatedByUser($user, $page, $limit);
    $items = array_map(fn(Message $m) => $this->formatResponse($m), $result['items']);

    return [
      'data' => $items,
      'meta' => $result['meta'],
    ];
  }

}

<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MessageRepository extends ServiceEntityRepository {
  public function __construct(
    ManagerRegistry                   $registry,
    private readonly PaginatorService $paginator,
  ) {
    parent::__construct($registry, Message::class);
  }

  /**
   * Returns paginated messages for a given user.
   */
  public function findPaginatedByUser(User $user, int $page = 1, int $limit = 20): array {
    $qb = $this->createQueryBuilder('m')
      ->andWhere('m.user = :user')
      ->setParameter('user', $user)
      ->orderBy('m.created_at', 'ASC');

    return $this->paginator->paginate($qb, $page, $limit);
  }
}

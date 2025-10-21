<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class PaginatorService {
  /**
   * Paginate any Doctrine QueryBuilder.
   *
   * @template T of object
   * @param QueryBuilder $qb
   * @param int $page
   * @param int $limit
   * @return array{items: T[], meta: array<string,int>}
   */
  public function paginate(QueryBuilder $qb, int $page = 1, int $limit = 20): array {
    $offset = ($page - 1) * $limit;

    $qb->setFirstResult($offset)
      ->setMaxResults($limit);

    $paginator = new Paginator($qb);
    $total = count($paginator);

    return [
      'items' => iterator_to_array($paginator),
      'meta' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'pages' => (int)ceil($total / $limit),
      ],
    ];
  }
}

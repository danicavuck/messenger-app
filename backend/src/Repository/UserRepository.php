<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, User::class);
  }

  /**
   * Check whether a user exists with the given email.
   */
  public function existsByEmail(string $email): bool {
    return (bool)$this->createQueryBuilder('u')
      ->select('1')
      ->andWhere('u.email = :email')
      ->setParameter('email', $email)
      ->getQuery()
      ->getOneOrNullResult();
  }

  /**
   * Find a user by email address.
   */
  public function findByEmail(string $email): ?User {
    return $this->createQueryBuilder('u')
      ->andWhere('u.email = :email')
      ->setParameter('email', $email)
      ->getQuery()
      ->getOneOrNullResult();
  }

  /**
   * Remove and optionally flush a user entity.
   */
  public function remove(User $user, bool $flush = false): void {
    $this->_em->remove($user);
    if ($flush) {
      $this->_em->flush();
    }
  }

  /**
   * Automatically rehash the user’s password over time.
   */
  public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
    if (!$user instanceof User) {
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
    }

    $user->setPassword($newHashedPassword);
    $this->save($user, true);
  }

  /**
   * Persist and optionally flush a user entity.
   */
  public function save(User $user, bool $flush = false): void {
    $this->_em->persist($user);
    if ($flush) {
      $this->_em->flush();
    }
  }
}

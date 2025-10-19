<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture {
  public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

  public function load(ObjectManager $om): void {
    $this->createUserIfNotExists(
      $om,
      email: 'demo@example.com',
      username: 'demouser',
      password: 'demo1234',
      roles: [Role::USER->value]
    );

    $this->createUserIfNotExists(
      $om,
      email: 'admin@example.com',
      username: 'admin',
      password: 'admin1234',
      roles: [Role::ADMIN->value]
    );
  }

  private function createUserIfNotExists(
    ObjectManager $om,
    string        $email,
    string        $username,
    string        $password,
    array         $roles
  ): void {
    $repository = $om->getRepository(User::class);
    $existing = $repository->findOneBy(['email' => $email]);

    if ($existing) {
      echo "User with email {$email} already exists, skipping fixture.\n";
      return;
    }

    $user = new User();
    $user->setEmail($email);
    $user->setUsername($username);
    $user->setRoles($roles);
    $user->setPassword($this->hasher->hashPassword($user, $password));

    $om->persist($user);
    $om->flush();

    echo "User {$email} created successfully with roles: " . implode(', ', $roles) . "\n";
  }
}

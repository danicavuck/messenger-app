<?php

namespace App\Service;

use App\DTO\LoginUserRequest;
use App\DTO\RegisterUserRequest;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\UserRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class AuthService {
  public function __construct(
    private UserRepository              $userRepository,
    private UserPasswordHasherInterface $hasher,
    private EntityManagerInterface      $em,
    private JWTEncoderInterface         $jwtEncoder,
    private ParameterBagInterface       $parameterBag,
    private JWTTokenManagerInterface    $jwtManager,

  ) {}

  /**
   * Register a new user.
   *
   * @throws BadRequestHttpException if user already exists
   */
  public function register(RegisterUserRequest $dto): void {
    if ($this->userRepository->existsByEmail($dto->email)) {
      throw new BadRequestHttpException('User with this email already exists.');
    }

    $user = new User();
    $user->setEmail($dto->email);
    $user->setUsername($dto->username);
    $user->setPassword($this->hasher->hashPassword($user, $dto->password));
    $user->setRoles([Role::USER->value]);

    $this->em->persist($user);
    $this->em->flush();
  }

  /**
   * @param LoginUserRequest $dto
   * @return array
   */
  public function login(LoginUserRequest $dto): array {
    $user = $this->userRepository->findByEmail($dto->email);

    if (!$user || !$this->hasher->isPasswordValid($user, $dto->password)) {
      throw new BadRequestHttpException('Invalid credentials.');
    }

    $accessTtl = $this->parameterBag->get('jwt.access_token_ttl');
    $refreshTtl = $this->parameterBag->get('jwt.refresh_token_ttl');

    $accessToken = $this->jwtManager->createFromPayload($user, [
      'type' => 'access',
      'exp' => (new DateTimeImmutable())->add(new DateInterval("PT{$accessTtl}S"))->getTimestamp(),
    ]);

    $refreshToken = $this->jwtManager->createFromPayload($user, [
      'type' => 'refresh',
      'exp' => (new DateTimeImmutable())->add(new DateInterval("PT{$refreshTtl}S"))->getTimestamp(),
    ]);

    return [
      'access_token' => $accessToken,
      'refresh_token' => $refreshToken,
      'user' => [
        'email' => $user->getEmail(),
        'username' => $user->getUsername(),
        'roles' => $user->getRoles(),
      ],
    ];
  }

  /**
   * @param string $refreshToken
   * @return array
   */
  public function refreshAccessToken(string $refreshToken): array {
    try {

      $refreshPayload = $this->jwtEncoder->decode($refreshToken);
    } catch (\Throwable $e) {
      throw new BadRequestHttpException('Invalid or malformed refresh token: ' . $e->getMessage());
    }

    if (($refreshPayload['type'] ?? null) !== 'refresh') {
      throw new BadRequestHttpException('Invalid refresh token type.');
    }

    if (($refreshPayload['exp'] ?? 0) < time()) {
      throw new BadRequestHttpException('Refresh token expired.');
    }

    $email = $refreshPayload['email'] ?? $refreshPayload['username'] ?? null;
    if (!$email) {
      throw new BadRequestHttpException('Refresh token missing email claim.');
    }

    $user = $this->userRepository->findOneBy(['email' => $email]);
    if (!$user) {
      throw new BadRequestHttpException('User not found for provided refresh token.');
    }

    // Generate new access token
    $accessTtl = $this->parameterBag->get('jwt.access_token_ttl');
    $newAccessToken = $this->jwtManager->createFromPayload($user, [
      'type' => 'access',
      'email' => $user->getEmail(),
      'exp' => (new DateTimeImmutable())->add(
        new DateInterval("PT{$accessTtl}S")
      )->getTimestamp(),
    ]);

    $rotate = $this->parameterBag->get('jwt.refresh_token_rotate');
    $newRefreshToken = $rotate
      ? $this->jwtManager->createFromPayload($user, [
        'type' => 'refresh',
        'email' => $user->getEmail(),
        'exp' => (new DateTimeImmutable())->add(
          new DateInterval("PT{$this->parameterBag->get('jwt.refresh_token_ttl')}S")
        )->getTimestamp(),
      ])
      : $refreshToken;

    return [
      'access_token' => $newAccessToken,
      'refresh_token' => $newRefreshToken,
    ];
  }
}

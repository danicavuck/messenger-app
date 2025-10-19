<?php

namespace App\Controller;

use App\DTO\LoginUserRequest;
use App\DTO\RefreshTokenRequest;
use App\DTO\RegisterUserRequest;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth')]
class AuthController extends AbstractController {
  public function __construct(
    private readonly AuthService $authService
  ) {}

  #[Route('/register', name: 'auth_register', methods: ['POST'])]
  public function register(RegisterUserRequest $dto): JsonResponse {
    $this->authService->register($dto);
    return $this->json(['message' => 'User registered successfully'], 201);
  }

  #[Route('/login', name: 'auth_login', methods: ['POST'])]
  public function login(LoginUserRequest $dto): JsonResponse {
    return $this->json($this->authService->login($dto));
  }

  #[Route('/refresh', name: 'auth_refresh', methods: ['POST'])]
  public function refresh(Request $request, RefreshTokenRequest $dto): JsonResponse {
    $authHeader = $request->headers->get('Authorization');

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      throw new BadRequestHttpException('Missing or invalid Authorization header.');
    }

    $result = $this->authService->refreshAccessToken($dto->refresh_token);

    return $this->json($result);
  }
}

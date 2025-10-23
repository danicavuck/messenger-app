<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users', name: 'user')]
class UserController extends AbstractController {

  #[Route('/me', name: 'me', methods: ['GET'])]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function me(#[CurrentUser] ?User $user): JsonResponse {
    if (!$user) {
      return $this->json(['error' => 'Not authenticated'], 401);
    }

    return $this->json($user, 200);
  }
}

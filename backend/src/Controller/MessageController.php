<?php

namespace App\Controller;

use App\DTO\MessageDTO;
use App\Entity\User;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController {
  public function __construct(private readonly MessageService $messageService) {}

  #[Route('/messages', name: 'get_user_messages', methods: ['GET'])]
  #[IsGranted('ROLE_USER')]
  public function getUserMessages(): JsonResponse {
    /** @var User $user */
    $user = $this->getUser();
    $data = $this->messageService->getUserMessages($user);

    return $this->json($data);
  }

  #[Route('/messages', name: 'create_message', methods: ['POST'])]
  #[IsGranted('ROLE_USER')]
  public function createMessage(MessageDTO $dto): JsonResponse {
    /** @var User $user */
    $user = $this->getUser();

    $message = $this->messageService->create($dto, $user);
    return $this->json($message, 201);
  }
}

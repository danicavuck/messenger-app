<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController {
  #[Route('/health', name: 'app_health', methods: ['GET'])]
  public function health(): Response {
    return new Response('OK', 200);
  }
}

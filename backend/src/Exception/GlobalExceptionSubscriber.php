<?php

namespace App\Exception;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class GlobalExceptionSubscriber implements EventSubscriberInterface {
  public function __construct(private SerializerInterface $serializer) {}

  public static function getSubscribedEvents(): array {
    return ['kernel.exception' => 'onException'];
  }

  public function onException(ExceptionEvent $event): void {
    $e = $event->getThrowable();
    $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

    $problem = [
      'type' => 'about:blank',
      'title' => $status >= 500 ? 'Server Error' : 'Request Error',
      'status' => $status,
      'detail' => $e->getMessage(),
      'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
    ];

    $event->setResponse(new JsonResponse($problem, $status));
  }
}

<?php

namespace App\Service;

use App\Event\MessageCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MessageCreatedSubscriber implements EventSubscriberInterface {
  public function __construct(private MessageRealtimePublisher $publisher) {}

  public static function getSubscribedEvents(): array {
    return [MessageCreatedEvent::NAME => 'onMessageCreated'];
  }

  public function onMessageCreated(MessageCreatedEvent $event): void {
    $this->publisher->publish($event->getMessage());
  }
}

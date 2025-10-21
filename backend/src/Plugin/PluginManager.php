<?php

namespace App\Plugin;

use App\Entity\Message;
use App\Event\MessageCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class PluginManager implements EventSubscriberInterface {
  /**
   * @param iterable<PluginInterface> $plugins
   */
  public function __construct(
    private iterable               $plugins,
    private EntityManagerInterface $em
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      MessageCreatedEvent::NAME => 'onMessageCreated',
    ];
  }

  public function onMessageCreated(MessageCreatedEvent $event): void {
    $message = $event->getMessage();
    $user = $event->getUser();

    foreach ($this->plugins as $plugin) {
      if ($plugin->supports($message, $user)) {
        $reply = $plugin->handle($message, $user);

        if ($reply instanceof Message) {
          $this->em->persist($reply);
        }
      }
    }

    $this->em->flush();
  }
}

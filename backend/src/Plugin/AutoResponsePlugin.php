<?php

namespace App\Plugin;

use App\Entity\Message;
use App\Entity\User;
use App\Enum\MessageStatus;

class AutoResponsePlugin implements PluginInterface {
  public function getName(): string {
    return 'Auto Response Plugin';
  }

  public function getDescription(): string {
    return 'Responds to greetings';
  }

  public function supports(Message $message, User $user): bool {
    $text = strtolower(trim($message->getContent()));
    return preg_match('/\b(hi|hello|hey)\b/', $text);
  }

  public function handle(Message $message, User $user): ?Message {
    $reply = new Message();
    $reply->setUser($user);
    $reply->setStatus(MessageStatus::SENT->value);
    $reply->setContent('Hi there! 🤖');
    $reply->setIsBot(true);
    return $reply;
  }
}

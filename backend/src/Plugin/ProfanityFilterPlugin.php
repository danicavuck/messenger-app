<?php

namespace App\Plugin;

use App\Entity\Message;
use App\Entity\User;

class ProfanityFilterPlugin implements PluginInterface {
  private array $banned = ['badword', 'idiot', 'stupid', 'javascript'];

  public function getName(): string {
    return 'Profanity Filter Plugin';
  }

  public function getDescription(): string {
    return 'Removes profanity from the message before saving.';
  }

  public function supports(Message $message, User $user): bool {
    $text = strtolower($message->getContent());
    foreach ($this->banned as $word) {
      if (str_contains($text, $word)) {
        return true;
      }
    }
    return false;
  }

  public function handle(Message $message, User $user): ?Message {
    $cleaned = $message->getContent();
    foreach ($this->banned as $word) {
      $cleaned = preg_replace('/' . preg_quote($word, '/') . '/i', '***', $cleaned);
    }
    $message->setContent($cleaned);
    return null;
  }
}

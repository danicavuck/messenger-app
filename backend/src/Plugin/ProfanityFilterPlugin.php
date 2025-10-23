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
    return 'Replaces banned words with the same number of asterisks.';
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
      $pattern = '/' . preg_quote($word, '/') . '/i';
      $cleaned = preg_replace_callback($pattern, function ($matches) {
        $length = strlen($matches[0]);
        return str_repeat('*', $length);
      }, $cleaned);
    }

    $message->setContent($cleaned);

    // returning null tells the plugin manager not to create a new message
    return null;
  }
}

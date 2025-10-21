<?php

namespace App\Plugin;

use App\Entity\Message;
use App\Entity\User;

interface PluginInterface {
  public function getName(): string;
  public function getDescription(): string;
  public function supports(Message $message, User $user): bool;
  public function handle(Message $message, User $user): ?Message;
}

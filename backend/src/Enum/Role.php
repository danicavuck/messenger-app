<?php

namespace App\Enum;

enum Role: string {
  case USER = 'ROLE_USER';
  case MODERATOR = 'ROLE_MODERATOR';
  case ADMIN = 'ROLE_ADMIN';
}

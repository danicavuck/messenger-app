<?php

namespace App\Enum;

enum MessageStatus: string {
  case SENT = 'sent';
  case RECEIVED = 'received';
}

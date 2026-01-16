<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Тип связи сообщения (ответ или пересылка).
 */
enum MessageLinkType: string
{
    case FORWARD = 'forward';
    case REPLY = 'reply';
}
<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Статус бота в чате.
 */
enum ChatStatus: string
{
    case ACTIVE = 'active';
    case REMOVED = 'removed';
    case LEFT = 'left';
    case CLOSED = 'closed';
}
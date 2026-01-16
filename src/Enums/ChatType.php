<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Тип чата.
 */
enum ChatType: string
{
    case CHAT = 'chat';
    case DIALOG = 'dialog';
}
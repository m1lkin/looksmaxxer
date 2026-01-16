<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Тип разметки текста.
 */
enum MarkupType: string
{
    case BOLD = 'bold';
    case ITALIC = 'italic';
    case STRIKETHROUGH = 'strikethrough';
    case UNDERLINE = 'underline';
    case CODE = 'code';
    case LINK = 'link';
    case USER_MENTION = 'user_mention';
}
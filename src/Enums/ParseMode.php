<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Режим парсинга текста сообщения (форматирование).
 */
enum ParseMode: string
{
    case MARKDOWN = 'markdown';
    case HTML = 'html';
}

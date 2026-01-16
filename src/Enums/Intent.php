<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Намерение кнопки (влияет на цвет/стиль).
 */
enum Intent: string
{
    case DEFAULT = 'default';
    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';
}

<?php

declare(strict_types=1);

namespace Looksmaxxer\Helpers;

/**
 * Хелпер для форматирования текста (ссылки, упоминания).
 */
class Formatting
{
    /**
     * Создает ссылку для упоминания пользователя в формате Markdown.
     *
     * @param int $userId ID пользователя.
     * @param string $name Отображаемое имя (рекомендуется полное имя из профиля).
     * @return string
     */
    public static function markdownMention(int $userId, string $name): string
    {
        return sprintf('[%s](max://max.ru/%d)', $name, $userId);
    }

    /**
     * Создает ссылку для упоминания пользователя в формате HTML.
     *
     * @param int $userId ID пользователя.
     * @param string $name Отображаемое имя.
     * @return string
     */
    public static function htmlMention(int $userId, string $name): string
    {
        return sprintf('<a href="max://max.ru/%d">%s</a>', $userId, $name);
    }

    /**
     * Создает Markdown ссылку.
     */
    public static function markdownLink(string $text, string $url): string
    {
        return sprintf('[%s](%s)', $text, $url);
    }

    /**
     * Создает HTML ссылку.
     */
    public static function htmlLink(string $text, string $url): string
    {
        return sprintf('<a href="%s">%s</a>', $url, $text);
    }
}

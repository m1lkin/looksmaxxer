<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Enums\UpdateType;

/**
 * Объект обновления (события).
 */
readonly class Update
{
    /**
     * @param UpdateType $updateType Тип события.
     * @param int $timestamp Время события.
     * @param Message|null $message Объект сообщения (для message_* событий).
     * @param User|null $sender Отправитель события (если применимо).
     * @param string|null $userLocale Локаль пользователя.
     * @param CallbackQuery|null $callback Данные callback-кнопки.
     * @param User|null $user Пользователь, затронутый событием (вход/выход).
     * @param string|null $chatId ID чата.
     * @param string|null $mid ID сообщения.
     * @param string|null $title Заголовок чата (для chat_title_changed).
     */
    public function __construct(
        public UpdateType $updateType,
        public int $timestamp,
        public ?Message $message = null,
        public ?User $sender = null,
        public ?string $userLocale = null,
        public ?CallbackQuery $callback = null,
        public ?User $user = null, // для событий user_added/removed
        public ?string $chatId = null,
        public ?string $mid = null, // для message_removed/edited
        public ?string $title = null, // для chat_title_changed
    ) {}

    public static function fromArray(array $data): self
    {
        $typeStr = (string)$data['update_type'];
        $type = UpdateType::tryFrom($typeStr) ?? $typeStr;

        return new self(
            updateType: $type instanceof UpdateType ? $type : UpdateType::MESSAGE_CREATED, // fallback
            timestamp: (int)$data['timestamp'],
            message: isset($data['message']) ? Message::fromArray($data['message']) : null,
            sender: isset($data['sender']) ? User::fromArray($data['sender']) : null,
            userLocale: $data['user_locale'] ?? null,
            callback: isset($data['callback']) ? CallbackQuery::fromArray($data['callback']) : null,
            user: isset($data['user']) ? User::fromArray($data['user']) : null,
            chatId: isset($data['chat_id']) ? (string)$data['chat_id'] : null,
            mid: isset($data['mid']) ? (string)$data['mid'] : null,
            title: $data['title'] ?? null,
        );
    }
}

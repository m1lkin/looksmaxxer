<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Enums\ChatType;

/**
 * Получатель сообщения.
 */
readonly class Recipient
{
    /**
     * @param int|null $chatId ID чата.
     * @param ChatType|null $chatType Тип чата.
     * @param int|null $userId ID пользователя.
     */
    public function __construct(
        public ?int $chatId = null,
        public ?ChatType $chatType = null,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            chatId: isset($data['chat_id']) ? (int)$data['chat_id'] : null,
            chatType: isset($data['chat_type']) ? ChatType::from($data['chat_type']) : null,
            userId: isset($data['user_id']) ? (int)$data['user_id'] : null,
        );
    }
}
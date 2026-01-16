<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Список чатов с маркером пагинации.
 */
class ChatList
{
    /**
     * @param Chat[] $chats Список чатов.
     * @param int|null $marker Маркер для следующей страницы.
     */
    public function __construct(
        public array $chats,
        public ?int $marker
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            chats: array_map(fn($chat) => Chat::fromArray($chat), $data['chats'] ?? []),
            marker: isset($data['marker']) ? (int)$data['marker'] : null,
        );
    }
}

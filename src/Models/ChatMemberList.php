<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Список участников чата с пагинацией.
 */
readonly class ChatMemberList
{
    /**
     * @param ChatMember[] $members Список участников.
     * @param int|null $marker Маркер для следующей страницы.
     */
    public function __construct(
        public array $members,
        public ?int $marker
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            members: array_map(fn($m) => ChatMember::fromArray($m), $data['members'] ?? []),
            marker: isset($data['marker']) ? (int)$data['marker'] : null,
        );
    }
}
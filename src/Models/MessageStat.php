<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Статистика сообщения.
 */
readonly class MessageStat
{
    /**
     * @param int $views Количество просмотров.
     */
    public function __construct(
        public int $views
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            views: (int)($data['views'] ?? 0)
        );
    }
}

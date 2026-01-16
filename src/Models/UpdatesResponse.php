<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Ответ метода получения обновлений.
 */
readonly class UpdatesResponse
{
    /**
     * @param Update[] $updates Список обновлений.
     * @param int|null $marker Маркер для следующей страницы.
     */
    public function __construct(
        public array $updates,
        public ?int $marker
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            updates: array_map(fn($u) => Update::fromArray($u), $data['updates'] ?? []),
            marker: isset($data['marker']) ? (int)$data['marker'] : null,
        );
    }
}
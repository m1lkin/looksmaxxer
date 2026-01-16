<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Enums\MarkupType;

/**
 * Элемент разметки текста.
 */
readonly class MarkupElement
{
    /**
     * @param MarkupType $type Тип разметки.
     * @param int $from Начальная позиция.
     * @param int $length Длина.
     * @param string|null $url URL (для ссылок).
     * @param int|null $userId ID пользователя (для упоминаний).
     */
    public function __construct(
        public MarkupType $type,
        public int $from,
        public int $length,
        public ?string $url = null,
        public ?int $userId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: MarkupType::tryFrom((string)$data['type']) ?? MarkupType::BOLD, // Fallback или ошибка
            from: (int)$data['from'],
            length: (int)$data['length'],
            url: $data['url'] ?? null,
            userId: isset($data['user_id']) ? (int)$data['user_id'] : null,
        );
    }
}
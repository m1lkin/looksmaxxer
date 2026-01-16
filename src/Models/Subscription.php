<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Информация о подписке на Webhook.
 */
readonly class Subscription
{
    /**
     * @param string $url URL вебхука.
     * @param int $time Время создания.
     * @param string[]|null $updateTypes Типы обновлений.
     */
    public function __construct(
        public string $url,
        public int $time,
        public ?array $updateTypes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            url: (string)$data['url'],
            time: (int)$data['time'],
            updateTypes: $data['update_types'] ?? null,
        );
    }
}
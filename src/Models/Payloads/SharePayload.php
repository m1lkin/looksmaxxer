<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Полезная нагрузка для кнопки "Поделиться" или расшаривания контента.
 */
readonly class SharePayload implements AttachmentPayload
{
    /**
     * @param string|null $url URL контента.
     * @param string|null $token Токен вложения.
     */
    public function __construct(
        public ?string $url = null,
        public ?string $token = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            token: $data['token'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'url' => $this->url,
            'token' => $this->token,
        ], fn($v) => $v !== null);
    }
}

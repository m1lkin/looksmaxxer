<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Полезная нагрузка для видео.
 */
readonly class VideoPayload implements AttachmentPayload
{
    /**
     * @param string|null $token Токен видео.
     */
    public function __construct(
        public ?string $token = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(token: $data['token'] ?? null);
    }

    public function toArray(): array
    {
        return array_filter(['token' => $this->token], fn($v) => $v !== null);
    }
}
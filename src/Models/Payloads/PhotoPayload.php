<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Полезная нагрузка для фото.
 */
readonly class PhotoPayload implements AttachmentPayload
{
    /**
     * @param string|null $url URL изображения.
     * @param string|null $token Токен изображения.
     * @param int|null $photoId ID фото.
     */
    public function __construct(
        public ?string $url = null,
        public ?string $token = null,
        public ?int $photoId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            token: $data['token'] ?? null,
            photoId: isset($data['photo_id']) ? (int)$data['photo_id'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'url' => $this->url,
            'token' => $this->token,
            'photo_id' => $this->photoId,
        ], fn($v) => $v !== null);
    }
}
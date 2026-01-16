<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Полезная нагрузка для стикера.
 */
readonly class StickerPayload implements AttachmentPayload
{
    /**
     * @param string $code Код стикера.
     */
    public function __construct(
        public string $code
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(code: (string)$data['code']);
    }

    public function toArray(): array
    {
        return ['code' => $this->code];
    }
}

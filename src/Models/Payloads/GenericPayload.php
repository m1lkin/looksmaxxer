<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Универсальная полезная нагрузка (для неизвестных типов).
 */
readonly class GenericPayload implements AttachmentPayload
{
    public function __construct(
        public array $data
    ) {}

    public function toArray(): array
    {
        return $this->data;
    }
}
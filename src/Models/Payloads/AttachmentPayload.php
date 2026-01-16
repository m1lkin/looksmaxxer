<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Интерфейс полезной нагрузки вложения.
 */
interface AttachmentPayload
{
    /**
     * Преобразовать в массив.
     * @return array
     */
    public function toArray(): array;
}
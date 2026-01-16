<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Объект изображения (иконка, аватар).
 */
readonly class Image
{
    /**
     * @param string $url URL изображения.
     */
    public function __construct(
        public string $url
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(url: (string)$data['url']);
    }
}
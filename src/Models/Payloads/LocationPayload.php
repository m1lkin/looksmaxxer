<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Payloads;

/**
 * Полезная нагрузка для геопозиции.
 */
readonly class LocationPayload implements AttachmentPayload
{
    /**
     * @param float $latitude Широта.
     * @param float $longitude Долгота.
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            latitude: (float)$data['latitude'],
            longitude: (float)$data['longitude']
        );
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}

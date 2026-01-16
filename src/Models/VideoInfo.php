<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Ссылки на видео в разных качествах.
 */
readonly class VideoUrls
{
    public function __construct(
        public ?string $mp4_1080 = null,
        public ?string $mp4_720 = null,
        public ?string $mp4_480 = null,
        public ?string $mp4_360 = null,
        public ?string $mp4_240 = null,
        public ?string $mp4_144 = null,
        public ?string $hls = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            mp4_1080: $data['mp4_1080'] ?? null,
            mp4_720: $data['mp4_720'] ?? null,
            mp4_480: $data['mp4_480'] ?? null,
            mp4_360: $data['mp4_360'] ?? null,
            mp4_240: $data['mp4_240'] ?? null,
            mp4_144: $data['mp4_144'] ?? null,
            hls: $data['hls'] ?? null,
        );
    }
}

/**
 * Информация о видео.
 */
readonly class VideoInfo
{
    /**
     * @param string $token Токен видео.
     * @param int $width Ширина.
     * @param int $height Высота.
     * @param int $duration Длительность (сек).
     * @param VideoUrls|null $urls Ссылки.
     * @param Image|null $thumbnail Превью.
     */
    public function __construct(
        public string $token,
        public int $width,
        public int $height,
        public int $duration,
        public ?VideoUrls $urls = null,
        public ?Image $thumbnail = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            token: (string)$data['token'],
            width: (int)$data['width'],
            height: (int)$data['height'],
            duration: (int)$data['duration'],
            urls: isset($data['urls']) ? VideoUrls::fromArray($data['urls']) : null,
            thumbnail: isset($data['thumbnail']) ? Image::fromArray($data['thumbnail']) : null,
        );
    }
}
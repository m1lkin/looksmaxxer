<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Models\Payloads\AttachmentPayload;
use Looksmaxxer\Models\Payloads\GenericPayload;
use Looksmaxxer\Models\Payloads\PhotoPayload;
use Looksmaxxer\Models\Payloads\VideoPayload;
use Looksmaxxer\Models\Payloads\StickerPayload;
use Looksmaxxer\Models\Payloads\ContactPayload;
use Looksmaxxer\Models\Payloads\LocationPayload;
use Looksmaxxer\Models\Payloads\SharePayload;

/**
 * Вложение сообщения.
 */
readonly class Attachment
{
    /**
     * @param string $type Тип вложения (image, video, audio, file, sticker, contact, location, share, inline_keyboard).
     * @param AttachmentPayload $payload Данные вложения.
     */
    public function __construct(
        public string $type,
        public AttachmentPayload $payload,
    ) {}

    /**
     * Создать вложение из массива данных.
     */
    public static function fromArray(array $data): self
    {
        $type = (string)$data['type'];
        $payloadData = (array)($data['payload'] ?? []);

        // Если это location, данные могут быть в корне объекта (судя по спецификации)
        if ($type === 'location' && empty($payloadData)) {
            $payloadData = $data;
        }

        $payload = match ($type) {
            'image', 'photo' => PhotoPayload::fromArray($payloadData),
            'video', 'audio', 'file' => VideoPayload::fromArray($payloadData), // UploadedInfo pattern
            'sticker' => StickerPayload::fromArray($payloadData),
            'contact' => ContactPayload::fromArray($payloadData),
            'location' => LocationPayload::fromArray($payloadData),
            'share' => SharePayload::fromArray($payloadData),
            default => new GenericPayload($payloadData),
        };

        return new self($type, $payload);
    }

    /**
     * Получить ID фото (если вложение - фото).
     */
    public function getPhotoId(): ?int
    {
        return $this->payload instanceof PhotoPayload ? $this->payload->photoId : null;
    }

    /**
     * Получить URL (если вложение содержит URL).
     */
    public function getUrl(): ?string
    {
        if ($this->payload instanceof PhotoPayload || $this->payload instanceof SharePayload) {
            return $this->payload->url;
        }
        return null;
    }
}

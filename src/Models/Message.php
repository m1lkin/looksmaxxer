<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Объект сообщения.
 */
readonly class Message
{
    /**
     * @param int $timestamp Время создания (unix time).
     * @param MessageBody $body Тело сообщения (текст, вложения и т.д.).
     * @param Recipient $recipient Получатель.
     * @param User|null $sender Отправитель.
     * @param LinkedMessage|null $link Ссылка (ответ/пересылка).
     * @param MessageStat|null $stat Статистика.
     * @param string|null $url Публичная ссылка.
     */
    public function __construct(
        public int $timestamp,
        public MessageBody $body,
        public Recipient $recipient,
        public ?User $sender = null,
        public ?LinkedMessage $link = null,
        public ?MessageStat $stat = null,
        public ?string $url = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            timestamp: (int)$data['timestamp'],
            // В документации сказано, что поле называется body. Но иногда (в getUpdates) оно может приходить как message?
            // Судя по последней доке - это body.
            body: MessageBody::fromArray($data['body'] ?? $data['message'] ?? []),
            recipient: Recipient::fromArray($data['recipient']),
            sender: isset($data['sender']) ? User::fromArray($data['sender']) : null,
            link: isset($data['link']) ? LinkedMessage::fromArray($data['link']) : null,
            stat: isset($data['stat']) ? MessageStat::fromArray($data['stat']) : null,
            url: $data['url'] ?? null,
        );
    }

    // --- Proxy methods for convenience ---

    /**
     * Получить ID сообщения (mid).
     */
    public function getMid(): string
    {
        return $this->body->mid;
    }

    /**
     * Получить текст сообщения.
     */
    public function getText(): ?string
    {
        return $this->body->text;
    }

    /**
     * Получить вложения.
     * @return Attachment[]|null
     */
    public function getAttachments(): ?array
    {
        return $this->body->attachments;
    }

    /**
     * Проверить, содержит ли сообщение вложение определенного типа.
     *
     * @param string $type Тип вложения (image, video, location, etc).
     * @return bool
     */
    public function hasAttachmentType(string $type): bool
    {
        if (empty($this->body->attachments)) {
            return false;
        }
        foreach ($this->body->attachments as $attachment) {
            if ($attachment->type === $type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получить данные геолокации, если они есть в сообщении.
     */
    public function getLocation(): ?Payloads\LocationPayload
    {
        foreach ($this->body->attachments ?? [] as $attachment) {
            if ($attachment->payload instanceof Payloads\LocationPayload) {
                return $attachment->payload;
            }
        }
        return null;
    }

    /**
     * Получить данные контакта, если они есть в сообщении.
     */
    public function getContact(): ?Payloads\ContactPayload
    {
        foreach ($this->body->attachments ?? [] as $attachment) {
            if ($attachment->payload instanceof Payloads\ContactPayload) {
                return $attachment->payload;
            }
        }
        return null;
    }

    /**
     * Получить данные стикера, если они есть в сообщении.
     */
    public function getSticker(): ?Payloads\StickerPayload
    {
        foreach ($this->body->attachments ?? [] as $attachment) {
            if ($attachment->payload instanceof Payloads\StickerPayload) {
                return $attachment->payload;
            }
        }
        return null;
    }

    /**
     * Получить первое изображение из сообщения.
     */
    public function getPhoto(): ?Payloads\PhotoPayload
    {
        foreach ($this->body->attachments ?? [] as $attachment) {
            if ($attachment->payload instanceof Payloads\PhotoPayload) {
                return $attachment->payload;
            }
        }
        return null;
    }
}
<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Тело сообщения.
 */
readonly class MessageBody
{
    /**
     * @param string $mid Уникальный ID сообщения.
     * @param int $seq Порядковый номер в чате.
     * @param string|null $text Текст сообщения.
     * @param Attachment[]|null $attachments Вложения.
     * @param MarkupElement[]|null $markup Разметка текста.
     */
    public function __construct(
        public string $mid,
        public int $seq,
        public ?string $text = null,
        public ?array $attachments = null,
        public ?array $markup = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            mid: (string)$data['mid'],
            seq: (int)$data['seq'],
            text: $data['text'] ?? null,
            attachments: isset($data['attachments']) 
                ? array_map(fn($a) => Attachment::fromArray($a), $data['attachments']) 
                : null,
            markup: isset($data['markup']) 
                ? array_map(fn($m) => MarkupElement::fromArray($m), $data['markup']) 
                : null,
        );
    }
}
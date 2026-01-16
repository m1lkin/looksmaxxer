<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Enums\MessageLinkType;

/**
 * Связанное сообщение (ответ или пересылка).
 */
readonly class LinkedMessage
{
    /**
     * @param MessageLinkType $type Тип связи (forward/reply).
     * @param User|null $sender Отправитель оригинального сообщения.
     * @param int|null $chatId ID чата оригинального сообщения.
     * @param MessageBody|null $message Тело оригинального сообщения.
     */
    public function __construct(
        public MessageLinkType $type,
        public ?User $sender = null,
        public ?int $chatId = null,
        public ?MessageBody $message = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: MessageLinkType::from($data['type']),
            sender: isset($data['sender']) ? User::fromArray($data['sender']) : null,
            chatId: isset($data['chat_id']) ? (int)$data['chat_id'] : null,
            message: isset($data['message']) ? MessageBody::fromArray($data['message']) : null,
        );
    }
}
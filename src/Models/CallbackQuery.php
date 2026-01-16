<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Данные callback-запроса (нажатие на кнопку).
 */
readonly class CallbackQuery
{
    /**
     * @param string $callbackId ID callback-запроса.
     * @param string $payload Полезная нагрузка кнопки.
     * @param User|null $user Пользователь, нажавший кнопку.
     * @param Message|null $message Сообщение, к которому привязана кнопка.
     */
    public function __construct(
        public string $callbackId,
        public string $payload,
        public ?User $user = null,
        public ?Message $message = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            callbackId: (string)$data['callback_id'],
            payload: (string)$data['payload'],
            user: isset($data['user']) ? User::fromArray($data['user']) : null,
            message: isset($data['message']) ? Message::fromArray($data['message']) : null,
        );
    }
}
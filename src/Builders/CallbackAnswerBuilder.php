<?php

declare(strict_types=1);

namespace Looksmaxxer\Builders;

/**
 * Билдер для ответа на Callback-запрос.
 */
class CallbackAnswerBuilder
{
    private array $data = [];

    public function __construct(string $callbackId)
    {
        $this->data['callback_id'] = $callbackId;
    }

    public static function create(string $callbackId): self
    {
        return new self($callbackId);
    }

    /**
     * Обновить сообщение, к которому привязана кнопка.
     *
     * @param MessageBuilder|array $message Билдер или массив данных сообщения.
     * @return self
     */
    public function message(MessageBuilder|array $message): self
    {
        $this->data['message'] = $message instanceof MessageBuilder ? $message->build() : $message;
        return $this;
    }

    /**
     * Показать одноразовое уведомление пользователю (Toast/Alert).
     *
     * @param string $text Текст уведомления.
     * @return self
     */
    public function notification(string $text): self
    {
        $this->data['notification'] = $text;
        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }
}
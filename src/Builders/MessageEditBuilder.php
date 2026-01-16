<?php

declare(strict_types=1);

namespace Looksmaxxer\Builders;

use Looksmaxxer\Enums\ParseMode;
use Looksmaxxer\Models\Attachment;

/**
 * Билдер для редактирования сообщения.
 */
class MessageEditBuilder
{
    private array $data = [];

    public function __construct(string $messageId)
    {
        $this->data['message_id'] = $messageId;
    }

    /**
     * Создать билдер редактирования.
     *
     * @param string $messageId ID сообщения для редактирования.
     * @return self
     */
    public static function create(string $messageId): self
    {
        return new self($messageId);
    }

    /**
     * Обновить текст сообщения.
     *
     * @param string $text Новый текст.
     * @return self
     */
    public function text(string $text): self
    {
        $this->data['text'] = $text;
        return $this;
    }

    /**
     * Обновить режим разметки.
     *
     * @param ParseMode $mode
     * @return self
     */
    public function format(ParseMode $mode): self
    {
        $this->data['format'] = $mode->value;
        return $this;
    }

    /**
     * Отправить уведомление об изменении.
     *
     * @param bool $notify
     * @return self
     */
    public function notify(bool $notify = true): self
    {
        $this->data['notify'] = $notify;
        return $this;
    }

    /**
     * Обновить вложения (заменяет текущие).
     *
     * @param Attachment[] $attachments Массив новых вложений.
     * @return self
     */
    public function attachments(array $attachments): self
    {
        $this->data['attachments'] = array_map(fn($a) => [
            'type' => $a->type,
            'payload' => $a->payload->toArray() // Важно вызывать toArray, так как в Edit ожидается массив
        ], $attachments);
        return $this;
    }

    /**
     * Удалить все вложения.
     *
     * @return self
     */
    public function clearAttachments(): self
    {
        $this->data['attachments'] = [];
        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }
}
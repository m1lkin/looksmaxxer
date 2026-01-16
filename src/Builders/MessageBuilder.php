<?php

declare(strict_types=1);

namespace Looksmaxxer\Builders;

use Looksmaxxer\Enums\ParseMode;
use Looksmaxxer\Enums\MessageLinkType;
use Looksmaxxer\Models\Attachment;

/**
 * Билдер для создания и отправки сообщений.
 */
class MessageBuilder
{
    private array $data = [];
    private array $queryParams = [];

    public function __construct(?string $chatId = null, ?int $userId = null)
    {
        if ($chatId) {
            $this->queryParams['chat_id'] = $chatId;
        }
        if ($userId) {
            $this->queryParams['user_id'] = $userId;
        }
    }

    /**
     * Создать новый билдер.
     *
     * @param string|null $chatId ID чата для отправки.
     * @param int|null $userId ID пользователя для отправки.
     * @return self
     */
    public static function create(?string $chatId = null, ?int $userId = null): self
    {
        return new self($chatId, $userId);
    }

    /**
     * Установить текст сообщения.
     *
     * @param string $text Текст сообщения (до 4000 символов).
     * @return self
     */
    public function text(string $text): self
    {
        $this->data['text'] = $text;
        return $this;
    }

    /**
     * Установить режим форматирования (HTML/Markdown).
     *
     * @param ParseMode $mode Режим разметки.
     * @return self
     */
    public function format(ParseMode $mode): self
    {
        $this->data['format'] = $mode->value;
        return $this;
    }

    /**
     * Отключить предпросмотр ссылок.
     *
     * @param bool $disable
     * @return self
     */
    public function disableLinkPreview(bool $disable = true): self
    {
        $this->queryParams['disable_link_preview'] = $disable;
        return $this;
    }

    /**
     * Отправить уведомление участникам (push).
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
     * Ответить на сообщение.
     *
     * @param string $messageId ID исходного сообщения.
     * @return self
     */
    public function replyTo(string $messageId): self
    {
        $this->data['link'] = [
            'type' => MessageLinkType::REPLY->value,
            'mid' => $messageId
        ];
        return $this;
    }

    /**
     * Переслать сообщение.
     *
     * @param string $messageId ID исходного сообщения.
     * @return self
     */
    public function forwardFrom(string $messageId): self
    {
        $this->data['link'] = [
            'type' => MessageLinkType::FORWARD->value,
            'mid' => $messageId
        ];
        return $this;
    }

    /**
     * Добавить вложение.
     *
     * @param Attachment $attachment Объект вложения.
     * @return self
     */
    public function addAttachment(Attachment $attachment): self
    {
        $this->data['attachments'][] = [
            'type' => $attachment->type,
            'payload' => $attachment->payload->toArray()
        ];
        return $this;
    }

    /**
     * Добавить инлайн-клавиатуру.
     *
     * @param \Looksmaxxer\Models\Keyboard\InlineKeyboard $keyboard Объект клавиатуры.
     * @return self
     */
    public function inlineKeyboard(\Looksmaxxer\Models\Keyboard\InlineKeyboard $keyboard): self
    {
        return $this->addAttachment(new Attachment(
            'inline_keyboard',
            new \Looksmaxxer\Models\Payloads\GenericPayload(['buttons' => $keyboard->toArray()])
        ));
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Прикрепить изображение по URL (хелпер).
     *
     * @param string $url URL изображения.
     * @return self
     */
    public function photoUrl(string $url): self
    {
        return $this->addAttachment(new Attachment(
            'image',
            new \Looksmaxxer\Models\Payloads\PhotoPayload(url: $url)
        ));
    }

    /**
     * Прикрепить изображение по токену (хелпер).
     *
     * @param string $token Токен изображения.
     * @return self
     */
    public function photoToken(string $token): self
    {
        return $this->addAttachment(new Attachment(
            'image',
            new \Looksmaxxer\Models\Payloads\PhotoPayload(token: $token)
        ));
    }

    /**
     * Прикрепить аудиофайл по токену.
     */
    public function audio(string $token): self
    {
        return $this->addAttachment(new Attachment(
            'audio',
            new \Looksmaxxer\Models\Payloads\VideoPayload(token: $token)
        ));
    }

    /**
     * Прикрепить видео по токену.
     */
    public function video(string $token): self
    {
        return $this->addAttachment(new Attachment(
            'video',
            new \Looksmaxxer\Models\Payloads\VideoPayload(token: $token)
        ));
    }

    /**
     * Прикрепить файл по токену.
     */
    public function file(string $token): self
    {
        return $this->addAttachment(new Attachment(
            'file',
            new \Looksmaxxer\Models\Payloads\VideoPayload(token: $token)
        ));
    }

    /**
     * Отправить стикер по его коду.
     */
    public function sticker(string $code): self
    {
        return $this->addAttachment(new Attachment(
            'sticker',
            new \Looksmaxxer\Models\Payloads\StickerPayload(code: $code)
        ));
    }

    /**
     * Отправить контакт.
     */
    public function contact(string $name, ?int $contactId = null, ?string $vcfInfo = null): self
    {
        return $this->addAttachment(new Attachment(
            'contact',
            new \Looksmaxxer\Models\Payloads\ContactPayload($name, $contactId, $vcfInfo)
        ));
    }

    /**
     * Отправить местоположение.
     */
    public function location(float $latitude, float $longitude): self
    {
        return $this->addAttachment(new Attachment(
            'location',
            new \Looksmaxxer\Models\Payloads\LocationPayload($latitude, $longitude)
        ));
    }

    /**
     * Отправить контент через Share.
     */
    public function share(?string $url = null, ?string $token = null): self
    {
        return $this->addAttachment(new Attachment(
            'share',
            new \Looksmaxxer\Models\Payloads\SharePayload($url, $token)
        ));
    }

    public function build(): array
    {
        return $this->data;
    }
}

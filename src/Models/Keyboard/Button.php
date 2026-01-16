<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Keyboard;

use Looksmaxxer\Enums\Intent;

/**
 * Кнопка инлайн-клавиатуры.
 */
class Button
{
    private function __construct(
        private readonly string $type,
        private readonly string $text,
        private readonly array $payload = []
    ) {}

    /**
     * Кнопка с callback-действием.
     *
     * @param string $text Текст кнопки.
     * @param string $payload Данные, которые вернутся в callback_id.
     * @param Intent $intent Стиль кнопки (default, positive, negative).
     */
    public static function callback(string $text, string $payload, Intent $intent = Intent::DEFAULT): self
    {
        return new self('callback', $text, [
            'payload' => $payload,
            'intent' => $intent->value
        ]);
    }

    /**
     * Кнопка-ссылка.
     *
     * @param string $text Текст кнопки.
     * @param string $url URL ссылки.
     */
    public static function link(string $text, string $url): self
    {
        return new self('link', $text, ['url' => $url]);
    }

    /**
     * Кнопка запроса геопозиции.
     *
     * @param string $text Текст кнопки.
     * @param bool $quick Отправлять без подтверждения (по умолчанию false).
     */
    public static function requestGeoLocation(string $text, bool $quick = false): self
    {
        return new self('request_geo_location', $text, ['quick' => $quick]);
    }

    /**
     * Кнопка запроса контакта.
     *
     * @param string $text Текст кнопки.
     */
    public static function requestContact(string $text): self
    {
        return new self('request_contact', $text);
    }

    /**
     * Кнопка открытия мини-приложения.
     *
     * @param string $text Текст кнопки.
     * @param string|null $webApp Username бота или ссылка.
     * @param int|null $contactId ID бота.
     * @param string|null $payload Параметры запуска (initData).
     */
    public static function openApp(string $text, ?string $webApp = null, ?int $contactId = null, ?string $payload = null): self
    {
        $data = [];
        if ($webApp !== null) $data['web_app'] = $webApp;
        if ($contactId !== null) $data['contact_id'] = $contactId;
        if ($payload !== null) $data['payload'] = $payload;

        return new self('open_app', $text, $data);
    }

    /**
     * Кнопка, отправляющая сообщение от имени пользователя.
     *
     * @param string $text Текст кнопки (он же будет отправлен).
     */
    public static function message(string $text): self
    {
        return new self('message', $text);
    }

    public function toArray(): array
    {
        return array_merge(
            ['type' => $this->type, 'text' => $this->text],
            $this->payload
        );
    }
}

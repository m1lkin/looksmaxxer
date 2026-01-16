<?php

declare(strict_types=1);

namespace Looksmaxxer\Builders;

/**
 * Билдер для создания подписки на Webhook.
 */
class SubscriptionBuilder
{
    private array $data = [];

    public function __construct(string $url)
    {
        $this->data['url'] = $url;
    }

    /**
     * Создать билдер.
     *
     * @param string $url URL вебхука (должен быть HTTPS).
     * @return self
     */
    public static function create(string $url): self
    {
        return new self($url);
    }

    /**
     * Указать типы обновлений, которые нужно получать.
     *
     * @param \Looksmaxxer\Enums\UpdateType[] $types Массив типов обновлений.
     * @return self
     */
    public function updateTypes(array $types): self
    {
        $this->data['update_types'] = array_map(fn($t) => $t->value, $types);
        return $this;
    }

    /**
     * Установить секретный ключ для подписи запросов (X-Max-Bot-Api-Secret).
     *
     * @param string $secret Секретная строка (5-256 символов, a-z, 0-9, -).
     * @return self
     */
    public function secret(string $secret): self
    {
        $this->data['secret'] = $secret;
        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }
}
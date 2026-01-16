<?php

declare(strict_types=1);

namespace Looksmaxxer\Builders;

/**
 * Билдер для обновления информации о чате.
 */
class ChatUpdateBuilder
{
    private array $data = [];

    public static function create(): self
    {
        return new self();
    }

    /**
     * Установить новое название чата.
     *
     * @param string $title Название (1-200 символов).
     * @return self
     */
    public function title(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Установить иконку чата по URL.
     *
     * @param string $url URL изображения.
     * @return self
     */
    public function iconUrl(string $url): self
    {
        $this->data['icon'] = ['url' => $url];
        return $this;
    }

    /**
     * Установить иконку чата по токену загруженного файла.
     *
     * @param string $token Токен файла.
     * @return self
     */
    public function iconToken(string $token): self
    {
        $this->data['icon'] = ['token' => $token];
        return $this;
    }

    /**
     * Установить иконку из списка токенов загруженных частей (для больших файлов).
     *
     * @param string[] $tokens Массив токенов.
     * @return self
     */
    public function iconPhotos(array $tokens): self
    {
        $this->data['icon'] = ['photos' => $tokens];
        return $this;
    }

    /**
     * Закрепить сообщение в чате.
     *
     * @param string $messageId ID сообщения.
     * @return self
     */
    public function pinMessage(string $messageId): self
    {
        $this->data['pin'] = $messageId;
        return $this;
    }

    /**
     * Включить/выключить уведомления об изменении.
     *
     * @param bool $notify
     * @return self
     */
    public function notify(bool $notify): self
    {
        $this->data['notify'] = $notify;
        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }
}
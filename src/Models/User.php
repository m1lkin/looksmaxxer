<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Объект пользователя (User, UserWithPhoto, BotInfo).
 */
readonly class User
{
    /**
     * @param int $id ID пользователя.
     * @param string $firstName Имя.
     * @param string|null $lastName Фамилия.
     * @param string|null $name Устаревшее поле имени.
     * @param string|null $username Юзернейм (без @).
     * @param bool $isBot Является ли ботом.
     * @param int|null $lastActivityTime Последняя активность (unix time ms).
     * @param string|null $description Описание (для UserWithPhoto/BotInfo).
     * @param string|null $avatarUrl URL аватара.
     * @param string|null $fullAvatarUrl URL большого аватара.
     * @param BotCommand[]|null $commands Список команд (для BotInfo).
     */
    public function __construct(
        public int $id,
        public string $firstName,
        public ?string $lastName = null,
        public ?string $name = null,
        public ?string $username = null,
        public bool $isBot = false,
        public ?int $lastActivityTime = null,
        public ?string $description = null,
        public ?string $avatarUrl = null,
        public ?string $fullAvatarUrl = null,
        public ?array $commands = null,
    ) {}

    /**
     * Создать из массива данных API.
     */
    public static function fromArray(array $data): self
    {
        $commands = null;
        if (isset($data['commands']) && is_array($data['commands'])) {
            $commands = array_map(fn($cmd) => BotCommand::fromArray($cmd), $data['commands']);
        }

        return new self(
            id: (int)($data['user_id'] ?? $data['id']),
            firstName: (string)($data['first_name'] ?? ''),
            lastName: $data['last_name'] ?? null,
            name: $data['name'] ?? null,
            username: $data['username'] ?? null,
            isBot: (bool)($data['is_bot'] ?? false),
            lastActivityTime: isset($data['last_activity_time']) ? (int)$data['last_activity_time'] : null,
            description: $data['description'] ?? null,
            avatarUrl: $data['avatar_url'] ?? null,
            fullAvatarUrl: $data['full_avatar_url'] ?? null,
            commands: $commands,
        );
    }
}

/**
 * Команда бота.
 */
readonly class BotCommand
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string)($data['name'] ?? $data['command'] ?? ''),
            description: $data['description'] ?? null,
        );
    }
}
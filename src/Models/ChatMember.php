<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Enums\ChatAdminPermission;

/**
 * Участник чата (расширенный объект пользователя).
 */
readonly class ChatMember
{
    /**
     * @param User $user Пользователь.
     * @param int $lastAccessTime Время последнего доступа.
     * @param bool $isOwner Является владельцем.
     * @param bool $isAdmin Является администратором.
     * @param int $joinTime Время вступления.
     * @param ChatAdminPermission[]|null $permissions Права администратора.
     * @param string|null $alias Алиас администратора.
     */
    public function __construct(
        public User $user,
        public int $lastAccessTime,
        public bool $isOwner,
        public bool $isAdmin,
        public int $joinTime,
        public ?array $permissions = null,
        public ?string $alias = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $permissions = null;
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $permissions = array_map(
                fn($p) => ChatAdminPermission::tryFrom($p) ?? $p, 
                $data['permissions']
            );
        }

        return new self(
            user: User::fromArray($data),
            lastAccessTime: (int)$data['last_access_time'],
            isOwner: (bool)$data['is_owner'],
            isAdmin: (bool)$data['is_admin'],
            joinTime: (int)$data['join_time'],
            permissions: $permissions,
            alias: $data['alias'] ?? null,
        );
    }
}

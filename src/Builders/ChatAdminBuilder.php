<?php

declare(strict_types=1);

namespace Looksmaxxer\Builders;

use Looksmaxxer\Enums\ChatAdminPermission;

/**
 * Билдер для назначения прав администратора.
 */
class ChatAdminBuilder
{
    private array $data;

    public function __construct(int $userId)
    {
        $this->data['user_id'] = $userId;
        $this->data['permissions'] = [];
    }

    /**
     * Создать билдер.
     *
     * @param int $userId ID пользователя.
     * @return self
     */
    public static function create(int $userId): self
    {
        return new self($userId);
    }

    /**
     * Установить список прав (перезаписывает текущие).
     *
     * @param ChatAdminPermission[] $permissions Массив прав.
     * @return self
     */
    public function permissions(array $permissions): self
    {
        $this->data['permissions'] = array_map(
            fn(ChatAdminPermission $p) => $p->value,
            $permissions
        );
        return $this;
    }

    /**
     * Добавить одно право к списку.
     *
     * @param ChatAdminPermission $permission Право.
     * @return self
     */
    public function addPermission(ChatAdminPermission $permission): self
    {
        $this->data['permissions'][] = $permission->value;
        return $this;
    }

    /**
     * Установить публичный алиас администратора (например, "Модератор").
     *
     * @param string $alias Алиас.
     * @return self
     */
    public function alias(string $alias): self
    {
        $this->data['alias'] = $alias;
        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }
}
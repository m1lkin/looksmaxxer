<?php

declare(strict_types=1);

namespace Looksmaxxer\Models\Keyboard;

/**
 * Инлайн-клавиатура для сообщений.
 */
class InlineKeyboard
{
    private array $rows = [];

    public static function create(): self
    {
        return new self();
    }

    /**
     * Добавляет строку кнопок. Можно передать одну или несколько кнопок.
     *
     * @param Button ...$buttons Кнопки в строке.
     * @return self
     */
    public function addRow(Button ...$buttons): self
    {
        $this->rows[] = $buttons;
        return $this;
    }

    /**
     * Преобразовать в массив (для отправки в API).
     * @return array
     */
    public function toArray(): array
    {
        return array_map(
            fn(array $row) => array_map(fn(Button $btn) => $btn->toArray(), $row),
            $this->rows
        );
    }
}
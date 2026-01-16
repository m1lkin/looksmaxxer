<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

/**
 * Ответ API о результате выполнения действия.
 */
readonly class ActionResponse
{
    /**
     * @param bool $success Успешно ли выполнено действие.
     * @param string|null $message Сообщение об ошибке (если success = false).
     */
    public function __construct(
        public bool $success,
        public ?string $message = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            success: (bool)($data['success'] ?? false),
            message: $data['message'] ?? null,
        );
    }
}
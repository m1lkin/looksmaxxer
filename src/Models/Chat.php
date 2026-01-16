<?php

declare(strict_types=1);

namespace Looksmaxxer\Models;

use Looksmaxxer\Enums\ChatType;
use Looksmaxxer\Enums\ChatStatus;

/**
 * Объект чата.
 */
readonly class Chat
{
    /**
     * @param int $id ID чата.
     * @param ChatType $type Тип чата.
     * @param ChatStatus $status Статус бота в чате.
     * @param string|null $title Название чата.
     * @param Image|null $icon Иконка.
     * @param int $lastEventTime Время последнего события.
     * @param int $participantsCount Количество участников.
     * @param bool $isPublic Публичный ли чат.
     * @param int|null $ownerId ID владельца.
     * @param string|null $link Ссылка приглашения.
     * @param string|null $description Описание.
     * @param Message|null $pinnedMessage Закрепленное сообщение.
     * @param User|null $dialogWithUser Собеседник (для диалогов).
     * @param string|null $chatMessageId ID сообщения, с которого начат чат.
     * @param array|null $participants Список участников (если запрошен).
     */
    public function __construct(
        public int $id,
        public ChatType $type,
        public ChatStatus $status,
        public ?string $title = null,
        public ?Image $icon = null,
        public int $lastEventTime = 0,
        public int $participantsCount = 0,
        public bool $isPublic = false,
        public ?int $ownerId = null,
        public ?string $link = null,
        public ?string $description = null,
        public ?Message $pinnedMessage = null,
        public ?User $dialogWithUser = null,
        public ?string $chatMessageId = null,
        public ?array $participants = null,
    ) {}

    /**
     * Создать из массива данных API.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['chat_id'],
            type: ChatType::from($data['type']),
            status: ChatStatus::from($data['status']),
            title: $data['title'] ?? null,
            icon: isset($data['icon']) ? Image::fromArray($data['icon']) : null,
            lastEventTime: (int)($data['last_event_time'] ?? 0),
            participantsCount: (int)($data['participants_count'] ?? 0),
            isPublic: (bool)($data['is_public'] ?? false),
            ownerId: isset($data['owner_id']) ? (int)$data['owner_id'] : null,
            link: $data['link'] ?? null,
            description: $data['description'] ?? null,
            pinnedMessage: isset($data['pinned_message']) ? Message::fromArray($data['pinned_message']) : null,
            dialogWithUser: isset($data['dialog_with_user']) ? User::fromArray($data['dialog_with_user']) : null,
            chatMessageId: $data['chat_message_id'] ?? null,
            participants: $data['participants'] ?? null,
        );
    }
}

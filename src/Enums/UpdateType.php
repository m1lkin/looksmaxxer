<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Тип события (обновления).
 */
enum UpdateType: string
{
    case MESSAGE_CREATED = 'message_created';
    case MESSAGE_CALLBACK = 'message_callback';
    case MESSAGE_EDITED = 'message_edited';
    case MESSAGE_REMOVED = 'message_removed';
    case BOT_ADDED = 'bot_added';
    case BOT_REMOVED = 'bot_removed';
    case DIALOG_MUTED = 'dialog_muted';
    case DIALOG_UNMUTED = 'dialog_unmuted';
    case DIALOG_CLEARED = 'dialog_cleared';
    case DIALOG_REMOVED = 'dialog_removed';
    case USER_ADDED = 'user_added';
    case USER_REMOVED = 'user_removed';
    case BOT_STARTED = 'bot_started';
    case BOT_STOPPED = 'bot_stopped';
    case CHAT_TITLE_CHANGED = 'chat_title_changed';
}
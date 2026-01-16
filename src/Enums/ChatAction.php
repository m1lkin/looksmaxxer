<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Действия бота в чате (статус набора текста и т.д.).
 */
enum ChatAction: string
{
    case TYPING_ON = 'typing_on';
    case SENDING_PHOTO = 'sending_photo';
    case SENDING_VIDEO = 'sending_video';
    case SENDING_AUDIO = 'sending_audio';
    case SENDING_FILE = 'sending_file';
    case MARK_SEEN = 'mark_seen';
}

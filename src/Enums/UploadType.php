<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Тип загружаемого файла.
 */
enum UploadType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case FILE = 'file';
}
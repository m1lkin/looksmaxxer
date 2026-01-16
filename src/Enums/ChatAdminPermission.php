<?php

declare(strict_types=1);

namespace Looksmaxxer\Enums;

/**
 * Права администратора чата.
 */
enum ChatAdminPermission: string
{
    case READ_ALL_MESSAGES = 'read_all_messages';
    case ADD_REMOVE_MEMBERS = 'add_remove_members';
    case ADD_ADMINS = 'add_admins';
    case CHANGE_CHAT_INFO = 'change_chat_info';
    case PIN_MESSAGE = 'pin_message';
    case WRITE = 'write';
    case CAN_CALL = 'can_call';
    case EDIT_LINK = 'edit_link';
    case POST_EDIT_DELETE_MESSAGE = 'post_edit_delete_message';
    case EDIT_MESSAGE = 'edit_message';
    case DELETE_MESSAGE = 'delete_message';
}
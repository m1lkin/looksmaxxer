<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Models;

use Looksmaxxer\Models\Chat;
use Looksmaxxer\Enums\ChatType;
use Looksmaxxer\Enums\ChatStatus;
use PHPUnit\Framework\TestCase;

class ChatTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'chat_id' => 100,
            'type' => 'chat',
            'status' => 'active',
            'title' => 'My Chat',
            'participants_count' => 5,
            'icon' => ['url' => 'http://img'],
        ];

        $chat = Chat::fromArray($data);

        $this->assertEquals(100, $chat->id);
        $this->assertEquals(ChatType::CHAT, $chat->type);
        $this->assertEquals(ChatStatus::ACTIVE, $chat->status);
        $this->assertEquals('My Chat', $chat->title);
        $this->assertEquals(5, $chat->participantsCount);
        $this->assertNotNull($chat->icon);
        $this->assertEquals('http://img', $chat->icon->url);
    }
}

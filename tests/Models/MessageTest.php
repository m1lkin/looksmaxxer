<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Models;

use Looksmaxxer\Models\Message;
use Looksmaxxer\Enums\ChatType;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'timestamp' => 1234567890,
            'recipient' => ['chat_id' => 100, 'chat_type' => 'chat'],
            'body' => [
                'mid' => 'MID_1',
                'seq' => 1,
                'text' => 'Hello',
                'attachments' => [
                    ['type' => 'image', 'payload' => ['url' => 'http://img']]
                ]
            ],
            'sender' => ['user_id' => 1, 'first_name' => 'User']
        ];

        $message = Message::fromArray($data);

        $this->assertEquals(1234567890, $message->timestamp);
        $this->assertEquals(100, $message->recipient->chatId);
        $this->assertEquals(ChatType::CHAT, $message->recipient->chatType);
        
        $this->assertEquals('MID_1', $message->getMid());
        $this->assertEquals('Hello', $message->getText());
        
        $attachments = $message->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertEquals('image', $attachments[0]->type);
        $this->assertEquals('http://img', $attachments[0]->getUrl());
        
        $this->assertNotNull($message->sender);
        $this->assertEquals(1, $message->sender->id);
    }
}

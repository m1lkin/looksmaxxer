<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Models;

use Looksmaxxer\Models\Update;
use Looksmaxxer\Enums\UpdateType;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'update_type' => 'message_created',
            'timestamp' => 111,
            'message' => [
                'timestamp' => 111,
                'recipient' => ['chat_id' => 1],
                'body' => ['mid' => '1', 'seq' => 1, 'text' => 'Hi']
            ]
        ];

        $update = Update::fromArray($data);

        $this->assertEquals(UpdateType::MESSAGE_CREATED, $update->updateType);
        $this->assertEquals(111, $update->timestamp);
        $this->assertNotNull($update->message);
        $this->assertEquals('Hi', $update->message->getText());
    }
}

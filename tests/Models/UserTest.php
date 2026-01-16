<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Models;

use PHPUnit\Framework\TestCase;
use Looksmaxxer\Models\User;
use Looksmaxxer\Models\BotCommand;

class UserTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'user_id' => 123,
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'is_bot' => true,
            'commands' => [
                ['name' => 'start', 'description' => 'Start bot']
            ]
        ];

        $user = User::fromArray($data);

        $this->assertEquals(123, $user->id);
        $this->assertEquals('Test', $user->firstName);
        $this->assertEquals('User', $user->lastName);
        $this->assertEquals('testuser', $user->username);
        $this->assertTrue($user->isBot);
        
        $this->assertIsArray($user->commands);
        $this->assertCount(1, $user->commands);
        $this->assertInstanceOf(BotCommand::class, $user->commands[0]);
        $this->assertEquals('start', $user->commands[0]->name);
    }
}

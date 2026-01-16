<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Builders;

use Looksmaxxer\Builders\SubscriptionBuilder;
use Looksmaxxer\Enums\UpdateType;
use PHPUnit\Framework\TestCase;

class SubscriptionBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = SubscriptionBuilder::create('https://hook.com')
            ->updateTypes([UpdateType::MESSAGE_CREATED, UpdateType::BOT_STARTED])
            ->secret('my_secret');

        $expected = [
            'url' => 'https://hook.com',
            'update_types' => ['message_created', 'bot_started'],
            'secret' => 'my_secret',
        ];

        $this->assertEquals($expected, $builder->build());
    }
}

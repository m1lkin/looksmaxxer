<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Builders;

use Looksmaxxer\Builders\MessageEditBuilder;
use Looksmaxxer\Enums\ParseMode;
use PHPUnit\Framework\TestCase;

class MessageEditBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = MessageEditBuilder::create('MID_123')
            ->text('Updated text')
            ->format(ParseMode::MARKDOWN)
            ->notify(false);

        $expected = [
            'message_id' => 'MID_123',
            'text' => 'Updated text',
            'format' => 'markdown',
            'notify' => false,
        ];

        $this->assertEquals($expected, $builder->build());
    }
}

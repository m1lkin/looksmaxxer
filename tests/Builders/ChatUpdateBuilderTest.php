<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Builders;

use Looksmaxxer\Builders\ChatUpdateBuilder;
use PHPUnit\Framework\TestCase;

class ChatUpdateBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = ChatUpdateBuilder::create()
            ->title('New Title')
            ->iconUrl('https://example.com/icon.png')
            ->pinMessage('MID_PIN')
            ->notify(true);

        $expected = [
            'title' => 'New Title',
            'icon' => ['url' => 'https://example.com/icon.png'],
            'pin' => 'MID_PIN',
            'notify' => true,
        ];

        $this->assertEquals($expected, $builder->build());
    }
}

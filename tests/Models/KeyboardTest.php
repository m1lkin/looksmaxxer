<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Models;

use Looksmaxxer\Models\Keyboard\Button;
use Looksmaxxer\Models\Keyboard\InlineKeyboard;
use PHPUnit\Framework\TestCase;

class KeyboardTest extends TestCase
{
    public function testButtonCreation(): void
    {
        $linkBtn = Button::link('Link', 'http://url');
        $this->assertEquals(['type' => 'link', 'text' => 'Link', 'url' => 'http://url'], $linkBtn->toArray());

        $cbBtn = Button::callback('Action', 'data');
        $this->assertEquals([
            'type' => 'callback', 
            'text' => 'Action', 
            'payload' => 'data',
            'intent' => 'default'
        ], $cbBtn->toArray());
    }

    public function testInlineKeyboardStructure(): void
    {
        $kb = InlineKeyboard::create()
            ->addRow(Button::link('A', 'u1'))
            ->addRow(Button::callback('B', 'p1'), Button::callback('C', 'p2'));

        $array = $kb->toArray();
        $this->assertCount(2, $array); // 2 rows
        $this->assertCount(1, $array[0]); // 1 btn in row 1
        $this->assertCount(2, $array[1]); // 2 btns in row 2
    }
}

<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Builders;

use Looksmaxxer\Builders\CallbackAnswerBuilder;
use PHPUnit\Framework\TestCase;

class CallbackAnswerBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = CallbackAnswerBuilder::create('cb_id')
            ->notification('Alert!');

        $expected = [
            'callback_id' => 'cb_id',
            'notification' => 'Alert!',
        ];

        $this->assertEquals($expected, $builder->build());
    }
}

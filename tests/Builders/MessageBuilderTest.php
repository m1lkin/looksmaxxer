<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Builders;

use Looksmaxxer\Builders\MessageBuilder;
use Looksmaxxer\Enums\ParseMode;
use Looksmaxxer\Enums\MessageLinkType;
use PHPUnit\Framework\TestCase;

class MessageBuilderTest extends TestCase
{
    public function testBuildsCorrectArray(): void
    {
        $builder = MessageBuilder::create(chatId: '12345')
            ->text('Hello')
            ->format(ParseMode::HTML)
            ->replyTo('987');

        $expectedData = [
            'text' => 'Hello',
            'format' => 'html',
            'link' => [
                'type' => MessageLinkType::REPLY->value,
                'mid' => '987'
            ]
        ];

        $expectedQuery = ['chat_id' => '12345'];

        $this->assertEquals($expectedData, $builder->build());
        $this->assertEquals($expectedQuery, $builder->getQueryParams());
    }

    public function testPhotoUrlHelper(): void
    {
        $builder = MessageBuilder::create()
            ->photoUrl('https://example.com/img.jpg');

        $data = $builder->build();
        
        $this->assertArrayHasKey('attachments', $data);
        $this->assertCount(1, $data['attachments']);
        $this->assertEquals('image', $data['attachments'][0]['type']);
        $this->assertEquals('https://example.com/img.jpg', $data['attachments'][0]['payload']['url']);
    }
}
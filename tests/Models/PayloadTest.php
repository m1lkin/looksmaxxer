<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests\Models;

use Looksmaxxer\Models\Payloads\PhotoPayload;
use Looksmaxxer\Models\Payloads\VideoPayload;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{
    public function testPhotoPayload(): void
    {
        $payload = new PhotoPayload(url: 'http://img', photoId: 123);
        $array = $payload->toArray();

        $this->assertEquals('http://img', $array['url']);
        $this->assertEquals(123, $array['photo_id']);
        $this->assertArrayNotHasKey('token', $array);

        $fromArray = PhotoPayload::fromArray(['token' => 'tok']);
        $this->assertEquals('tok', $fromArray->token);
    }

    public function testVideoPayload(): void
    {
        $payload = new VideoPayload(token: 'vid_tok');
        $this->assertEquals(['token' => 'vid_tok'], $payload->toArray());
    }
}

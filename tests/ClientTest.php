<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Looksmaxxer\Client;
use Looksmaxxer\Enums\ChatAction;
use Looksmaxxer\Enums\UploadType;
use Looksmaxxer\Models\Chat;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        return new Client('token', 'https://api.test', ['handler' => $handlerStack]);
    }

    public function testGetMe(): void
    {
        $client = $this->createMockClient([
            new Response(200, [], json_encode(['user_id' => 1, 'first_name' => 'Bot']))
        ]);

        $me = $client->getMe();
        $this->assertEquals(1, $me->id);
    }

    public function testGetChats(): void
    {
        $client = $this->createMockClient([
            new Response(200, [], json_encode([
                'chats' => [
                    ['chat_id' => 100, 'type' => 'chat', 'status' => 'active', 'title' => 'Chat 1']
                ],
                'marker' => 50
            ]))
        ]);

        $list = $client->getChats(count: 10);
        $this->assertCount(1, $list->chats);
        $this->assertEquals(100, $list->chats[0]->id);
        $this->assertEquals(50, $list->marker);
    }

    public function testSendChatAction(): void
    {
        $client = $this->createMockClient([
            new Response(200, [], json_encode(['success' => true]))
        ]);

        $response = $client->sendChatAction('100', ChatAction::TYPING_ON);
        $this->assertTrue($response->success);
    }

    public function testUploadFile(): void
    {
        // 1. URL request
        // 2. File upload
        $client = $this->createMockClient([
            new Response(200, [], json_encode(['url' => 'http://upload', 'token' => 'vid_token'])),
            new Response(200, [], json_encode(['token' => 'final_token']))
        ]);

        // Create a dummy file
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'content');

        try {
            $attachment = $client->uploadFile($tmpFile, UploadType::VIDEO);
            
            $this->assertEquals('video', $attachment->type);
            // Video token comes from first request
            $this->assertEquals('vid_token', $attachment->payload->toArray()['token']);
        } finally {
            unlink($tmpFile);
        }
    }
}
<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Looksmaxxer\Bot;
use Looksmaxxer\Client;
use Looksmaxxer\Enums\UpdateType;
use Looksmaxxer\Models\Update;
use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{
    public function testRouteUpdate(): void
    {
        // Мокаем ответ getUpdates
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'marker' => 1,
                'updates' => [
                    [
                        'update_type' => 'message_created',
                        'timestamp' => 123,
                        'message' => [
                            'timestamp' => 123,
                            'recipient' => ['chat_id' => 1],
                            'body' => ['mid' => '1', 'seq' => 1, 'text' => 'Hello']
                        ]
                    ]
                ]
            ])),
            // Второй вызов пустой
            new Response(200, [], json_encode(['marker' => 1, 'updates' => []]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client('token', 'https://api.test', ['handler' => $handlerStack]);
        
        $bot = new Bot($client);
        
        $called = false;
        
        $bot->onMessage(function (Update $update, Client $c) use (&$called, $bot) {
            $called = true;
            $this->assertEquals('Hello', $update->message->getText());
            $bot->stop(); 
        });

        $bot->start(timeout: 1);
        
        $this->assertTrue($called);
    }

    public function testCommandFilter(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'marker' => 1,
                'updates' => [
                    [
                        'update_type' => 'message_created',
                        'timestamp' => 123,
                        'message' => [
                            'timestamp' => 123,
                            'recipient' => ['chat_id' => 1],
                            'body' => ['mid' => '1', 'seq' => 1, 'text' => '/start']
                        ]
                    ],
                    [
                        'update_type' => 'message_created',
                        'timestamp' => 124,
                        'message' => [
                            'timestamp' => 124,
                            'recipient' => ['chat_id' => 1],
                            'body' => ['mid' => '2', 'seq' => 2, 'text' => 'Not a command']
                        ]
                    ]
                ]
            ])),
            // Второй вызов, на случай если первый цикл пройдет быстрее
            new Response(200, [], json_encode(['marker' => 1, 'updates' => []]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client('token', 'https://api.test', ['handler' => $handlerStack]);
        
        $bot = new Bot($client);
        $commandCount = 0;
        $totalProcessed = 0;

        // Считаем команды /start
        $bot->onCommand('start', function() use (&$commandCount) {
            $commandCount++;
        });

        // Считаем все сообщения, чтобы знать когда остановиться
        $bot->onMessage(function() use (&$totalProcessed, $bot) {
            $totalProcessed++;
            if ($totalProcessed >= 2) {
                $bot->stop();
            }
        });

        $bot->start(timeout: 1);
        
        $this->assertEquals(1, $commandCount);
        $this->assertEquals(2, $totalProcessed);
    }
}
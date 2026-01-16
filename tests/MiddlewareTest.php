<?php

declare(strict_types=1);

namespace Looksmaxxer\Tests;

use Looksmaxxer\Bot;
use Looksmaxxer\Client;
use Looksmaxxer\Models\Update;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    public function testMiddlewareChain(): void
    {
        $client = new Client('token');
        $bot = new Bot($client);
        
        $log = [];
        
        $bot->use(function(Update $u, Client $c, $next) use (&$log) {
            $log[] = 'm1_start';
            $next($u, $c);
            $log[] = 'm1_end';
        });

        $bot->onMessage(function() use (&$log) {
            $log[] = 'handler';
        });

        $update = Update::fromArray([
            'update_type' => 'message_created',
            'timestamp' => 1,
            'message' => ['timestamp' => 1, 'recipient' => ['chat_id' => 1], 'body' => ['mid' => '1', 'seq' => 1]]
        ]);

        $bot->processUpdate($update);

        $this->assertEquals(['m1_start', 'handler', 'm1_end'], $log);
    }
}

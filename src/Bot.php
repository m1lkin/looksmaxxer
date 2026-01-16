<?php

declare(strict_types=1);

namespace Looksmaxxer;

use Looksmaxxer\Enums\UpdateType;
use Looksmaxxer\Models\Update;

/**
 * Класс Bot для высокоуровневой работы с API.
 * Реализует механизм Long Polling, Webhook и маршрутизацию событий.
 */
class Bot
{
    private Client $client;
    /** @var array<string, callable[]> Обработчики событий */
    private array $handlers = [];
    /** @var callable[] Промежуточное ПО (Middleware) */
    private array $middlewares = [];
    /** @var bool Флаг работы цикла обновлений */
    private bool $running = false;
    /** @var int|null Текущий маркер обновлений */
    private ?int $marker = null;
    /** @var callable|null Обработчик ошибок */
    private $errorHandler = null;

    /**
     * Конструктор бота.
     *
     * @param string|Client $tokenOrClient Токен бота или готовый экземпляр Client.
     */
    public function __construct(string|Client $tokenOrClient)
    {
        if ($tokenOrClient instanceof Client) {
            $this->client = $tokenOrClient;
        } else {
            $this->client = new Client($tokenOrClient);
        }
    }

    /**
     * Получить экземпляр клиента API.
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Добавить промежуточное ПО (middleware).
     * Middleware получает ($update, $client, $next) и должен вызвать $next($update, $client).
     */
    public function use(callable $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Установить маркер вручную (например, загрузить из БД).
     */
    public function setMarker(?int $marker): self
    {
        $this->marker = $marker;
        return $this;
    }

    /**
     * Получить текущий маркер (например, для сохранения в БД).
     */
    public function getMarker(): ?int
    {
        return $this->marker;
    }

    /**
     * Подписаться на определенный тип события.
     */
    public function on(UpdateType $type, callable $handler): self
    {
        $this->handlers[$type->value][] = $handler;
        return $this;
    }

    /**
     * Установить глобальный обработчик ошибок для цикла start().
     */
    public function onError(callable $handler): self
    {
        $this->errorHandler = $handler;
        return $this;
    }

    // --- Удобные методы фильтрации (Sugar) ---

    public function onMessage(callable $handler): self
    {
        return $this->on(UpdateType::MESSAGE_CREATED, $handler);
    }

    public function onCallback(callable $handler): self
    {
        return $this->on(UpdateType::MESSAGE_CALLBACK, $handler);
    }

    public function onCallbackQuery(string $pattern, callable $handler): self
    {
        return $this->onCallback(function (Update $update, Client $client) use ($pattern, $handler) {
            $payload = $update->callback?->payload ?? '';
            $isRegex = str_starts_with($pattern, '/') && strlen($pattern) > 1;
            
            if ($isRegex) {
                if (preg_match($pattern, $payload)) {
                    call_user_func($handler, $update, $client);
                }
            } elseif ($payload === $pattern) {
                call_user_func($handler, $update, $client);
            }
        });
    }

    public function onCommand(string $command, callable $handler): self
    {
        $command = '/' . ltrim($command, '/');
        return $this->onMessage(function (Update $update, Client $client) use ($command, $handler) {
            $text = $update->message?->getText() ?? '';
            $parts = explode(' ', $text);
            $inputCommand = $parts[0] ?? '';

            if (strcasecmp($inputCommand, $command) === 0) {
                $args = array_slice($parts, 1);
                call_user_func($handler, $update, $client, $args);
            }
        });
    }

    public function onText(string $pattern, callable $handler): self
    {
        return $this->onMessage(function (Update $update, Client $client) use ($pattern, $handler) {
            $text = $update->message?->getText();
            if ($text && preg_match($pattern, $text)) {
                call_user_func($handler, $update, $client);
            }
        });
    }

    public function onPhoto(callable $handler): self
    {
        return $this->onMessage(function (Update $update, Client $client) use ($handler) {
            if ($update->message?->hasAttachmentType('image')) {
                call_user_func($handler, $update, $client);
            }
        });
    }

    public function onVideo(callable $handler): self
    {
        return $this->onMessage(function (Update $update, Client $client) use ($handler) {
            if ($update->message?->hasAttachmentType('video')) {
                call_user_func($handler, $update, $client);
            }
        });
    }

    public function onLocation(callable $handler): self
    {
        return $this->onMessage(function (Update $update, Client $client) use ($handler) {
            if ($update->message?->hasAttachmentType('location')) {
                call_user_func($handler, $update, $client);
            }
        });
    }

    public function onContact(callable $handler): self
    {
        return $this->onMessage(function (Update $update, Client $client) use ($handler) {
            if ($update->message?->hasAttachmentType('contact')) {
                call_user_func($handler, $update, $client);
            }
        });
    }

    public function onUserJoined(callable $handler): self
    {
        return $this->on(UpdateType::USER_ADDED, $handler);
    }

    public function onUserRemoved(callable $handler): self
    {
        return $this->on(UpdateType::USER_REMOVED, $handler);
    }

    // --- Логика работы ---

    /**
     * Обработать входящий вебхук.
     */
    public function handleWebhook(): void
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return;
        }

        $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        
        if (isset($data[0]) || (isset($data['updates']) && is_array($data['updates']))) {
            $updates = $data['updates'] ?? $data;
            foreach ($updates as $updateData) {
                $this->processUpdate(Update::fromArray($updateData));
            }
            return;
        }

        if (isset($data['update_type'])) {
            $this->processUpdate(Update::fromArray($data));
        }
    }

    /**
     * Запустить цикл Long Polling.
     */
    public function start(int $timeout = 30): void
    {
        $this->running = true;
        $types = !empty($this->handlers) 
            ? array_map(fn($t) => UpdateType::from($t), array_keys($this->handlers))
            : null;

        while ($this->running) {
            try {
                $response = $this->client->getUpdates(
                    timeout: $timeout,
                    marker: $this->marker,
                    types: $types
                );

                if ($response->marker !== null) {
                    $this->marker = $response->marker;
                }

                foreach ($response->updates as $update) {
                    $this->processUpdate($update);
                }
            } catch (\Exception $e) {
                if ($this->errorHandler) {
                    call_user_func($this->errorHandler, $e);
                } else {
                    echo "Bot Error: " . $e->getMessage() . PHP_EOL;
                    sleep(2);
                }
            }
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }

    /**
     * Обработать обновление через Pipeline.
     */
    public function processUpdate(Update $update): void
    {
        $pipeline = $this->middlewares;
        $pipeline[] = function (Update $update, Client $client) {
            $this->executeHandlers($update, $client);
        };

        $this->callPipeline($pipeline, $update, $this->client);
    }

    private function callPipeline(array $pipeline, Update $update, Client $client): void
    {
        if (empty($pipeline)) {
            return;
        }

        $current = array_shift($pipeline);
        $next = function (Update $u, Client $c) use ($pipeline) {
            $this->callPipeline($pipeline, $u, $c);
        };

        call_user_func($current, $update, $client, $next);
    }

    private function executeHandlers(Update $update, Client $client): void
    {
        $type = $update->updateType->value;
        if (isset($this->handlers[$type])) {
            foreach ($this->handlers[$type] as $handler) {
                call_user_func($handler, $update, $client);
            }
        }
    }
}

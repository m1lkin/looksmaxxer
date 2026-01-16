# Быстрый Старт

Это руководство поможет вам быстро начать работу с библиотекой Looksmaxxer для создания ботов на платформе MAX.

## Шаг 1: Установка

В папке вашего проекта выполните команду:

```bash
composer require m1lkin/looksmaxxer
```

## Шаг 2: Создание простого эхо-бота

Создайте файл `bot.php` со следующим содержимым:

```php
<?php

require 'vendor/autoload.php';

use Looksmaxxer\Client;
use Looksmaxxer\Builders\MessageBuilder;
use Looksmaxxer\Enums\ParseMode;

// 1. Инициализация клиента
$token = 'ВАШ_ТОКЕН'; // Получите токен у BotFather платформы MAX
$client = new Client($token);

echo "Бот запущен: " . $client->getMe()->name . PHP_EOL;

// 2. Получение обновлений (Long Polling симуляция)
// В реальном проекте используйте Webhook или цикл
while (true) {
    try {
        $updates = $client->getUpdates(limit: 10, offset: 0);
        
        foreach ($updates['result'] ?? [] as $update) {
            $message = $update['message'] ?? null;
            
            if ($message && isset($message['text'])) {
                $chatId = $message['chat']['id'];
                $text = $message['text'];
                
                echo "Получено сообщение: $text из чата $chatId" . PHP_EOL;

                // 3. Отправка ответа
                $builder = MessageBuilder::create((string)$chatId)
                    ->text("Вы сказали: " . $text);

                $client->sendMessage($builder);
            }
        }
        
        sleep(2); // Пауза между запросами
    } catch (Exception $e) {
        echo "Ошибка: " . $e->getMessage() . PHP_EOL;
        sleep(5);
    }
}
```

## Шаг 3: Запуск

Запустите бота в терминале:

```bash
php bot.php
```

Теперь напишите вашему боту сообщение, и он ответит вам тем же текстом.

## Что дальше?

- Изучите `README.md` для полного списка методов.
- Посмотрите папку `examples/` (если есть) или тесты в `tests/` для примеров кода.
- Используйте автодополнение вашей IDE, так как библиотека полностью типизирована.

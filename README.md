# Looksmaxxer

Мощная, типизированная и удобная PHP библиотека для платформы ботов MAX.

## Особенности

- **Bot Framework:** Встроенный класс `Bot` с поддержкой событий (`onMessage`, `onLocation`, `onCommand` и др.).
- **Режимы работы:** Поддержка как **Long Polling** (разработка), так и **Webhook** (продакшен).
- **Строгая типизация:** Использует PHP 8.2+, Enums и DTO.
- **Удобные геттеры:** Доступ к координатам и контактам через методы `$message->getLocation()`.
- **Безопасность:** Никаких "сырых" массивов при работе с клавиатурами и медиа.

## Установка

```bash
composer require m1lkin/looksmaxxer
```

## Быстрый старт

### 1. Обработка геолокации и контактов

Когда пользователь нажимает кнопку запроса данных, боту приходит сообщение с вложением.

```php
use Looksmaxxer\Bot;
use Looksmaxxer\Models\Update;
use Looksmaxxer\Client;
use Looksmaxxer\Models\Keyboard\InlineKeyboard;
use Looksmaxxer\Models\Keyboard\Button;
use Looksmaxxer\Builders\MessageBuilder;

$bot = new Bot('TOKEN');

// 1. Отправляем кнопку запроса
$bot->onCommand('start', function(Update $update, Client $client) {
    $kb = InlineKeyboard::create()
        ->addRow(Button::requestGeoLocation('Где я?', quick: true));
        
    $client->sendMessage(
        MessageBuilder::create(chatId: $update->chatId)
            ->text('Нажми на кнопку ниже:')
            ->inlineKeyboard($kb)
    );
});

// 2. Обрабатываем ответ
$bot->onLocation(function(Update $update, Client $client) {
    $location = $update->message->getLocation();
    echo "Широта: {$location->latitude}, Долгота: {$location->longitude}";
});

// Запуск (Long Polling для тестов)
$bot->start();
```

## Использование в Production (Webhook)

Для боевого режима создайте файл (например, `webhook.php`), настройте веб-сервер на этот файл и укажите его URL в настройках бота.

```php
// webhook.php
require 'vendor/autoload.php';

use Looksmaxxer\Bot;

$bot = new Bot('TOKEN');

$bot->onMessage(function($update, $client) {
    // Ваша логика
    $client->sendMessage(
        \Looksmaxxer\Builders\MessageBuilder::create(chatId: $update->chatId)->text('Привет из Webhook!')
    );
});

// Обработка входящего запроса
$bot->handleWebhook();
```

## Фильтрация кнопок (Callback)

```php
$bot->onCallbackQuery('confirm_order', function(Update $update, Client $client) {
    // Пользователь нажал кнопку с payload "confirm_order"
    $client->sendMessage(
        MessageBuilder::create(chatId: $update->chatId)->text('Заказ подтвержден!')
    );
});
```

## Команды и Текст

```php
$bot->onCommand('help', function($u, $c) { /* ... */ });

$bot->onText('/купи|продай/i', function($u, $c) {
    // Сработает на сообщения "купи", "Продай" и т.д.
});
```

## Тестирование

```bash
vendor/bin/phpunit
```

## Лицензия

MIT
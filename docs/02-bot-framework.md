# Часть 2: Bot Framework (События и Логика)

Класс `Bot` — это сердце вашего приложения. Он берет на себя самую сложную работу: получение обновлений, их сортировку и запуск нужного кода. В этом разделе мы подробно разберем, как писать логику бота.

## Содержание
1. [Система событий](#система-событий)
2. [Обработка сообщений (Текст и Команды)](#обработка-сообщений-текст-и-команды)
3. [Обработка нажатий на кнопки (Callbacks)](#обработка-нажатий-на-кнопки-callbacks)
4. [Обработка вложений (Фото, Видео, Гео)](#обработка-вложений-фото-видео-гео)
5. [Системные события](#системные-события)
6. [Middleware (Продвинутая обработка)](#middleware-продвинутая-обработка)
7. [Обработка ошибок](#обработка-ошибок)

---

## Система событий

Когда что-то происходит (пришло сообщение, нажали кнопку), создается объект `Update`. Бот смотрит на этот объект и решает, какую функцию запустить.

Базовый синтаксис регистрации обработчика:

```php
$bot->on(UpdateType::MESSAGE_CREATED, function (Update $update, Client $client) {
    // Ваш код
});
```

Но чтобы вам было удобнее, мы сделали специальные методы-хелперы для каждого случая.

---

## Обработка сообщений (Текст и Команды)

### 1. Любое сообщение
Метод `onMessage` срабатывает на **каждое** входящее сообщение (текст, фото, стикер и т.д.).

```php
$bot->onMessage(function ($update, $client) {
    // Получаем текст сообщения
    $text = $update->message->getText();
    // Получаем ID чата
    $chatId = $update->chatId;
    
    echo "Сообщение в чате $chatId: $text\n";
});
```

### 2. Команды (`/start`, `/help`)
Метод `onCommand` срабатывает, только если сообщение начинается с указанной команды. Он также автоматически разбивает текст на аргументы.

```php
// Сработает на "/gift user123 500"
$bot->onCommand('gift', function ($update, $client, $args) {
    // $args будет равен ['user123', '500']
    
    if (count($args) < 2) {
        $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
            ->text("Использование: /gift <кому> <сумма>"));
        return;
    }
    
    $user = $args[0];
    $amount = $args[1];
    
    // Логика подарка...
    $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
        ->text("Подарок $amount монет отправлен пользователю $user!"));
});
```

### 3. Текст по шаблону (Regex)
Метод `onText` позволяет реагировать на фразы, соответствующие регулярному выражению.

```php
// Сработает на "цена", "цены", "прайс" (регистронезависимо)
$bot->onText('/(цена|прайс)/iu', function ($update, $client) {
    $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
        ->text("Наш прайс-лист: ..."));
});
```

---

## Обработка нажатий на кнопки (Callbacks)

Когда пользователь нажимает на `Inline`-кнопку, боту не приходит сообщение. Ему приходит `message_callback`.

### 1. Обработка всех кнопок
```php
$bot->onCallback(function ($update, $client) {
    $callbackId = $update->callback->callbackId; // ID нажатия (нужен для ответа)
    $data = $update->callback->payload; // То, что вы зашили в кнопку
    
    echo "Нажата кнопка с данными: $data\n";
    
    // ВАЖНО: На callback нужно ответить, иначе у пользователя будет крутиться спиннер
    $client->answerCallback(
        Looksmaxxer\Builders\CallbackAnswerBuilder::create($callbackId)
            ->notification("Кнопка обработана!") // Всплывашка
    );
});
```

### 2. Фильтрация кнопок
Метод `onCallbackQuery` позволяет обрабатывать только конкретные кнопки.

```php
// Сработает, если payload кнопки равен 'buy_apple'
$bot->onCallbackQuery('buy_apple', function ($update, $client) {
    // Логика покупки яблока
});

// Сработает, если payload похож на 'set_age_25' (Regex)
$bot->onCallbackQuery('/^set_age_(\d+)$/', function ($update, $client) {
    // Парсим возраст из payload
    preg_match('/^set_age_(\d+)$/', $update->callback->payload, $matches);
    $age = $matches[1];
    
    $client->answerCallback(
        Looksmaxxer\Builders\CallbackAnswerBuilder::create($update->callback->callbackId)
            ->notification("Возраст установлен: $age")
    );
});
```

---

## Обработка вложений (Фото, Видео, Гео)

Бот может фильтровать сообщения по типу контента.

```php
// Если прислали фото
$bot->onPhoto(function ($update, $client) {
    // Получаем объект PhotoPayload первого фото
    $photo = $update->message->getPhoto();
    $fileUrl = $photo->url;
    
    $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
        ->text("Классное фото! Ссылка: $fileUrl"));
});

// Если прислали геолокацию
$bot->onLocation(function ($update, $client) {
    $geo = $update->message->getLocation();
    $lat = $geo->latitude;
    $lon = $geo->longitude;
    
    $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
        ->text("Координаты получены: $lat, $lon"));
});

// Другие методы:
// $bot->onVideo(...)
// $bot->onContact(...)
// $bot->onSticker(...)
// $bot->onFile(...)
```

---

## Системные события

Это события, не связанные с отправкой сообщений пользователями.

*   `onUserJoined`: В чат зашел новый участник.
*   `onUserRemoved`: Участник покинул чат или был исключен.
*   `onBotStarted`: Пользователь нажал "Запустить" в ЛС с ботом (или разблокировал его).
*   `onBotStopped`: Пользователь заблокировал бота.
*   `onChatTitleChanged`: В чате изменилось название.

Пример приветствия:
```php
$bot->onUserJoined(function ($update, $client) {
    $newUser = $update->user; // Объект User
    $name = $newUser->firstName;
    
    $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
        ->text("Добро пожаловать, $name!"));
});
```

---

## Middleware (Продвинутая обработка)

**Middleware (Промежуточное ПО)** — это функции, которые выполняются **до** основных обработчиков. Представьте это как слои луковицы, через которые проходит событие.

**Зачем это нужно?**
1.  **Логирование:** Записывать все входящие апдейты.
2.  **Обработка ошибок:** Глобальный try-catch.
3.  **Авторизация:** Проверять, есть ли пользователь в базе данных.
4.  **Черный список:** Игнорировать сообщения от забаненных.

**Пример (Логирование):**

```php
$bot->use(function ($update, $client, $next) {
    // Этот код выполняется ДО обработчиков
    echo "[LOG] Пришло событие типа: " . $update->updateType->value . "\n";
    
    // Передаем управление дальше по цепочке
    $next($update, $client);
    
    // Этот код выполнится ПОСЛЕ обработчиков
    echo "[LOG] Обработка завершена.\n";
});
```

**Пример (Блокировка техработ):**

```php
$maintenanceMode = true;

$bot->use(function ($update, $client, $next) use ($maintenanceMode) {
    if ($maintenanceMode && $update->chatId) {
        $client->sendMessage(Looksmaxxer\Builders\MessageBuilder::create($update->chatId)
            ->text("Бот на техническом обслуживании."));
        // НЕ вызываем $next(), поэтому остальные обработчики НЕ сработают
        return; 
    }
    
    $next($update, $client);
});
```

---

## Обработка ошибок

В режиме Long Polling скрипт работает вечно. Если произойдет ошибка (исключение), скрипт может упасть. Чтобы этого не случилось, используйте `onError`.

```php
$bot->onError(function (\Exception $e) {
    // Запишем ошибку в файл, а не выведем на экран
    file_put_contents('errors.log', date('Y-m-d H:i:s') . " " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Бот продолжит работу!
});
```
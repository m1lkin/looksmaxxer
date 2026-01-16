<?php

declare(strict_types=1);

namespace Looksmaxxer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Looksmaxxer\Exceptions\MaxException;
use Looksmaxxer\Models\User;
use Looksmaxxer\Models\Chat;
use Looksmaxxer\Models\ChatList;
use Looksmaxxer\Models\ChatMember;
use Looksmaxxer\Builders\MessageBuilder;
use Looksmaxxer\Enums\ChatAction;

/**
 * Основной класс для взаимодействия с API платформы MAX.
 */
class Client
{
    private GuzzleClient $http;

    /**
     * Конструктор клиента.
     *
     * @param string $token Токен бота.
     * @param string $baseUrl Базовый URL API (по умолчанию https://platform-api.max.ru).
     * @param array $guzzleOptions Дополнительные опции для Guzzle клиента (например, для тестов или прокси).
     */
    public function __construct(
        private readonly string $token,
        string $baseUrl = 'https://platform-api.max.ru',
        array $guzzleOptions = []
    ) {
        $defaultOptions = [
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'headers' => [
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30.0,
        ];

        $this->http = new GuzzleClient(array_replace_recursive($defaultOptions, $guzzleOptions));
    }

    /**
     * Внутренний метод для выполнения запросов.
     *
     * @param string $method HTTP метод (GET, POST, etc).
     * @param string $uri URI ресурса.
     * @param array $options Опции запроса.
     * @return array Декодированный JSON ответ.
     * @throws MaxException При ошибках сети или API.
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $uri, $options);
            $content = $response->getBody()->getContents();
            
            if (empty($content)) {
                return [];
            }

            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new MaxException("Network error [{$method} {$uri}]: " . $e->getMessage(), $e->getCode(), $e);
        } catch (\JsonException $e) {
            throw new MaxException("Invalid JSON response: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    // --- Bots ---

    /**
     * Получение информации о текущем боте.
     *
     * @return User
     * @throws MaxException
     */
    public function getMe(): User
    {
        $data = $this->request('GET', 'me');
        return User::fromArray($data);
    }

    // --- Chats ---

    /**
     * Получение списка чатов бота.
     *
     * @param int $count Количество чатов для получения (макс 100).
     * @param int|null $marker Маркер для пагинации (из предыдущего запроса).
     * @return ChatList
     * @throws MaxException
     */
    public function getChats(int $count = 50, ?int $marker = null): ChatList
    {
        $query = ['count' => $count];
        if ($marker !== null) {
            $query['marker'] = $marker;
        }

        $data = $this->request('GET', 'chats', [
            RequestOptions::QUERY => $query
        ]);
        
        return ChatList::fromArray($data);
    }

    /**
     * Получение информации о чате по ссылке (invite link).
     *
     * @param string $link Ссылка или часть ссылки (например, @mychat или mychat).
     * @return Chat
     * @throws MaxException
     */
    public function getChatByLink(string $link): Chat
    {
        // Убираем @ если есть, так как в доке сказано что символ @ опционален
        $link = ltrim($link, '@');
        $data = $this->request('GET', "chats/{$link}");
        return Chat::fromArray($data);
    }

    /**
     * Получение информации о чате по ID.
     *
     * @param string $chatId ID чата.
     * @return Chat
     * @throws MaxException
     */
    public function getChat(string $chatId): Chat
    {
        $data = $this->request('GET', "chats/{$chatId}");
        return Chat::fromArray($data);
    }

    /**
     * Обновление информации о чате.
     *
     * @param string $chatId ID чата.
     * @param Builders\ChatUpdateBuilder $builder Билдер с обновляемыми данными.
     * @return Chat Обновленный объект чата.
     * @throws MaxException
     */
    public function updateChat(string $chatId, Builders\ChatUpdateBuilder $builder): Chat
    {
        $response = $this->request('PATCH', "chats/{$chatId}", [
            RequestOptions::JSON => $builder->build()
        ]);
        return Chat::fromArray($response);
    }

    /**
     * Удаление чата (для всех участников).
     *
     * @param string $chatId ID чата.
     * @return Models\ActionResponse Результат операции.
     * @throws MaxException
     */
    public function deleteChat(string $chatId): Models\ActionResponse
    {
        $data = $this->request('DELETE', "chats/{$chatId}");
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Отправка действия бота в чат (например, "печатает...").
     *
     * @param string $chatId ID чата.
     * @param ChatAction $action Действие.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function sendChatAction(string $chatId, ChatAction $action): Models\ActionResponse
    {
        $data = $this->request('POST', "chats/{$chatId}/actions", [
            RequestOptions::JSON => ['action' => $action->value]
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Получение закрепленного сообщения в чате.
     *
     * @param string $chatId ID чата.
     * @return Models\Message|null Сообщение или null, если ничего не закреплено.
     * @throws MaxException
     */
    public function getPinnedMessage(string $chatId): ?Models\Message
    {
        $data = $this->request('GET', "chats/{$chatId}/pin");
        return isset($data['message']) ? Models\Message::fromArray($data['message']) : null;
    }

    /**
     * Закрепление сообщения в чате.
     *
     * @param string $chatId ID чата.
     * @param string $messageId ID сообщения.
     * @param bool $notify Отправить уведомление участникам (по умолчанию true).
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function pinMessage(string $chatId, string $messageId, bool $notify = true): Models\ActionResponse
    {
        $data = $this->request('PUT', "chats/{$chatId}/pin", [
            RequestOptions::JSON => [
                'message_id' => $messageId,
                'notify' => $notify
            ]
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Открепление сообщения в чате.
     *
     * @param string $chatId ID чата.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function unpinMessage(string $chatId): Models\ActionResponse
    {
        $data = $this->request('DELETE', "chats/{$chatId}/pin");
        return Models\ActionResponse::fromArray($data);
    }

    // --- Members ---

    /**
     * Получение информации о членстве бота в чате.
     *
     * @param string $chatId ID чата.
     * @return Models\ChatMember
     * @throws MaxException
     */
    public function getMyMemberInfo(string $chatId): Models\ChatMember
    {
        $data = $this->request('GET', "chats/{$chatId}/members/me");
        return Models\ChatMember::fromArray($data);
    }

    /**
     * Выход бота из чата.
     *
     * @param string $chatId ID чата.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function leaveChat(string $chatId): Models\ActionResponse
    {
        $data = $this->request('DELETE', "chats/{$chatId}/members/me");
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Получение списка администраторов чата.
     *
     * @param string $chatId ID чата.
     * @return Models\ChatMemberList
     * @throws MaxException
     */
    public function getChatAdmins(string $chatId): Models\ChatMemberList
    {
        $data = $this->request('GET', "chats/{$chatId}/members/admins");
        return Models\ChatMemberList::fromArray($data);
    }

    /**
     * Назначение администраторов чата.
     *
     * @param string $chatId ID чата.
     * @param Builders\ChatAdminBuilder[] $adminBuilders Массив билдеров с настройками админов.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function promoteAdmins(string $chatId, array $adminBuilders): Models\ActionResponse
    {
        $adminsData = array_map(fn($b) => $b->build(), $adminBuilders);
        
        $data = $this->request('POST', "chats/{$chatId}/members/admins", [
            RequestOptions::JSON => ['admins' => $adminsData]
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Разжалование администратора (лишение прав).
     *
     * @param string $chatId ID чата.
     * @param string $userId ID пользователя.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function demoteAdmin(string $chatId, string $userId): Models\ActionResponse
    {
        $data = $this->request('DELETE', "chats/{$chatId}/members/admins/{$userId}");
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Получение списка участников чата.
     *
     * @param string $chatId ID чата.
     * @param int[]|null $userIds Опциональный список ID пользователей для проверки членства.
     * @param int|null $marker Маркер пагинации.
     * @param int $count Количество участников.
     * @return Models\ChatMemberList
     * @throws MaxException
     */
    public function getChatMembers(string $chatId, ?array $userIds = null, ?int $marker = null, int $count = 20): Models\ChatMemberList
    {
        $query = ['count' => $count];
        if ($userIds !== null) {
            $query['user_ids'] = $userIds;
        }
        if ($marker !== null) {
            $query['marker'] = $marker;
        }

        $data = $this->request('GET', "chats/{$chatId}/members", [
            RequestOptions::QUERY => $query
        ]);

        return Models\ChatMemberList::fromArray($data);
    }

    /**
     * Добавление участников в чат.
     *
     * @param string $chatId ID чата.
     * @param int[] $userIds Массив ID пользователей.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function addChatMembers(string $chatId, array $userIds): Models\ActionResponse
    {
        $data = $this->request('POST', "chats/{$chatId}/members", [
            RequestOptions::JSON => ['user_ids' => $userIds]
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Удаление участника из чата.
     *
     * @param string $chatId ID чата.
     * @param int $userId ID пользователя.
     * @param bool $block Заблокировать пользователя (только для приватных/публичных ссылок).
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function kickChatMember(string $chatId, int $userId, bool $block = false): Models\ActionResponse
    {
        $data = $this->request('DELETE', "chats/{$chatId}/members", [
            RequestOptions::QUERY => [
                'user_id' => $userId,
                'block' => $block
            ]
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Получение списка активных вебхуков (подписок).
     *
     * @return Models\Subscription[]
     * @throws MaxException
     */
    public function getSubscriptions(): array
    {
        $data = $this->request('GET', 'subscriptions');
        return array_map(fn($s) => Models\Subscription::fromArray($s), $data['subscriptions'] ?? []);
    }

    /**
     * Подписка на вебхуки.
     *
     * @param Builders\SubscriptionBuilder $builder Билдер с настройками подписки.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function subscribe(Builders\SubscriptionBuilder $builder): Models\ActionResponse
    {
        $data = $this->request('POST', 'subscriptions', [
            RequestOptions::JSON => $builder->build()
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Отписка от вебхука.
     *
     * @param string $url URL вебхука для удаления.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function unsubscribe(string $url): Models\ActionResponse
    {
        $data = $this->request('DELETE', 'subscriptions', [
            RequestOptions::QUERY => ['url' => $url]
        ]);
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Получение обновлений (Long Polling).
     *
     * @param int $limit Лимит обновлений.
     * @param int $timeout Тайм-аут ожидания (сек).
     * @param int|null $marker Маркер пагинации.
     * @param Enums\UpdateType[]|null $types Фильтр типов обновлений.
     * @return Models\UpdatesResponse
     * @throws MaxException
     */
    public function getUpdates(
        int $limit = 100, 
        int $timeout = 30, 
        ?int $marker = null, 
        ?array $types = null
    ): Models\UpdatesResponse {
        $query = [
            'limit' => $limit,
            'timeout' => $timeout,
        ];
        
        if ($marker !== null) {
            $query['marker'] = $marker;
        }
        
        if ($types !== null) {
            $query['types'] = implode(',', array_map(fn($t) => $t->value, $types));
        }

        $data = $this->request('GET', 'updates', [
            RequestOptions::QUERY => $query
        ]);
        
        return Models\UpdatesResponse::fromArray($data);
    }

    // --- Upload ---

    /**
     * Загрузка файла.
     * Происходит в два этапа: получение URL и сама загрузка.
     *
     * @param string $filePath Путь к локальному файлу.
     * @param Enums\UploadType $type Тип загружаемого файла.
     * @return Models\Attachment Объект вложения, готовый к отправке.
     * @throws MaxException
     */
    public function uploadFile(string $filePath, Enums\UploadType $type): Models\Attachment
    {
        // 1. Получаем URL для загрузки
        $uploadInfo = $this->request('POST', 'uploads', [
            RequestOptions::QUERY => ['type' => $type->value]
        ]);

        $uploadUrl = $uploadInfo['url'];
        // Для видео и аудио токен приходит сразу
        $token = $uploadInfo['token'] ?? null;

        // 2. Загружаем файл
        // Guzzle автоматически выставит правильный Content-Type с boundary, если мы передадим multipart
        // Но нам нужно сбросить application/json, установленный в конструкторе
        $response = $this->http->post($uploadUrl, [
            RequestOptions::HEADERS => [
                'Content-Type' => null, // Даем Guzzle самому выставить multipart/form-data
            ],
            RequestOptions::MULTIPART => [
                [
                    'name'     => 'data', // В документации поле называется "data"
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath)
                ]
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        // 3. Определяем итоговый токен
        // Если это фото или файл, токен приходит в ответе на загрузку
        if ($type === Enums\UploadType::IMAGE || $type === Enums\UploadType::FILE) {
            $token = $result['token'];
        }

        $payload = match ($type) {
            Enums\UploadType::IMAGE => new Models\Payloads\PhotoPayload(token: $token),
            Enums\UploadType::VIDEO, Enums\UploadType::AUDIO => new Models\Payloads\VideoPayload(token: $token),
            default => new Models\Payloads\GenericPayload(['token' => $token]),
        };

        // Возвращаем готовое вложение
        return new Models\Attachment(
            type: $type->value,
            payload: $payload
        );
    }

    // --- Messages ---

    /**
     * Получение сообщений.
     *
     * @param string|null $chatId ID чата.
     * @param int[]|null $messageIds Список ID сообщений.
     * @param int|null $from Timestamp от.
     * @param int|null $to Timestamp до.
     * @param int $count Количество сообщений.
     * @return Models\Message[]
     * @throws MaxException
     */
    public function getMessages(
        ?string $chatId = null,
        ?array $messageIds = null,
        ?int $from = null,
        ?int $to = null,
        int $count = 50
    ): array {
        $query = ['count' => $count];
        
        if ($chatId !== null) {
            $query['chat_id'] = $chatId;
        }
        
        if ($messageIds !== null) {
            $query['message_ids'] = implode(',', $messageIds);
        }
        
        if ($from !== null) {
            $query['from'] = $from;
        }
        
        if ($to !== null) {
            $query['to'] = $to;
        }

        $data = $this->request('GET', 'messages', [
            RequestOptions::QUERY => $query
        ]);

        return array_map(fn($m) => Models\Message::fromArray($m), $data['messages'] ?? []);
    }

    /**
     * Получение одного сообщения по ID.
     *
     * @param string $messageId ID сообщения.
     * @return Models\Message
     * @throws MaxException
     */
    public function getMessage(string $messageId): Models\Message
    {
        $data = $this->request('GET', "messages/{$messageId}");
        return Models\Message::fromArray($data);
    }

    /**
     * Отправка сообщения.
     *
     * @param Builders\MessageBuilder $builder Билдер сообщения.
     * @return Models\Message Отправленное сообщение.
     * @throws MaxException
     */
    public function sendMessage(Builders\MessageBuilder $builder): Models\Message
    {
        $data = $this->request('POST', 'messages', [
            RequestOptions::QUERY => $builder->getQueryParams(),
            RequestOptions::JSON => $builder->build(),
        ]);
        
        return Models\Message::fromArray($data);
    }

    /**
     * Редактирование сообщения.
     *
     * @param Builders\MessageEditBuilder $builder Билдер редактирования.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function editMessage(Builders\MessageEditBuilder $builder): Models\ActionResponse
    {
        $body = $builder->build();
        $data = $this->request('PUT', 'messages', [
            RequestOptions::QUERY => ['message_id' => $body['message_id']],
            RequestOptions::JSON => $body
        ]);
        
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Удаление сообщения.
     *
     * @param string $messageId ID сообщения.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function deleteMessage(string $messageId): Models\ActionResponse
    {
        $data = $this->request('DELETE', 'messages', [
            RequestOptions::QUERY => ['message_id' => $messageId]
        ]);
        
        return Models\ActionResponse::fromArray($data);
    }

    /**
     * Получение информации о видео-вложении.
     *
     * @param string $videoToken Токен видео.
     * @return Models\VideoInfo
     * @throws MaxException
     */
    public function getVideoInfo(string $videoToken): Models\VideoInfo
    {
        $data = $this->request('GET', "videos/{$videoToken}");
        return Models\VideoInfo::fromArray($data);
    }

    /**
     * Ответ на callback (нажатие кнопки).
     *
     * @param Builders\CallbackAnswerBuilder $builder Билдер ответа.
     * @return Models\ActionResponse
     * @throws MaxException
     */
    public function answerCallback(Builders\CallbackAnswerBuilder $builder): Models\ActionResponse
    {
        $data = $this->request('POST', 'answers', [
            RequestOptions::JSON => $builder->build()
        ]);
        
        return Models\ActionResponse::fromArray($data);
    }
}

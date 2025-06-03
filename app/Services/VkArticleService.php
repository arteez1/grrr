<?php

namespace App\Services;

use App\Models\Post;
use App\Models\VkArticle;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class VkArticleService
{
    public static function publish(VkArticle $article): void
    {
        $accessToken = config('services.vk.access_token');
        $groupId = config('services.vk.group_id');

        // Проверка конфигурации
        if (!$accessToken || !$groupId) {
            throw new \RuntimeException('Настройки VK не настроены! Проверьте .env');
        }

        $vk = new VKApiClient();

        try {
            // 1. Создание вики-страницы
            $page = $vk->pages()->save($accessToken, [
                'group_id' => $groupId,
                'title' => $article->title,
                'text' => $article->content,
            ]);

            // 2. Генерация короткой ссылки
            $shortUrl = $vk->utils()->getShortLink($accessToken, [
                'url' => "https://vk.com/page-{$groupId}_{$page['id']}",
            ])['short_url'];

            // 3. Публикация поста (если требуется)
            if ($article->link_to_post) {
                $post = $vk->wall()->post($accessToken, [
                    'owner_id' => "-{$groupId}",
                    'message' => "[{$shortUrl}|{$article->title}] #статья",
                    'attachments' => "page{$groupId}_{$page['id']}",
                ]);
            }
            // После публикации статьи в VK:
            self::autoPublishPostVk($article);

            // 4. Обновление записи
            $article->update([
                'vk_article_id' => $page['id'],
                'short_url' => $shortUrl,
                'vk_post_id' => $post['post_id'] ?? null,
                'is_published' => true,
            ]);

        } catch (VKApiException $e) {  //(ошибки API)
            $errorMessage = "Ошибка VK API: " . $e->getMessage();
            Log::error($errorMessage, ['article_id' => $article->id]);
            throw new \RuntimeException(self::parseVkError($e->getMessage()));
        } catch (VKClientException $e) { //(сетевые ошибки)
            $errorMessage = "Сетевая ошибка: " . $e->getMessage();
            Log::error($errorMessage);
            throw new \RuntimeException('Ошибка подключения к VK. Попробуйте позже.');
        } catch (\Exception $e) { //Общие исключения
            Log::error('Неизвестная ошибка: ' . $e->getMessage());
            throw new \RuntimeException('Произошла непредвиденная ошибка.');
        }
    }

    /**
     * Парсинг ошибок VK API для пользователя
     */
    private static function parseVkError(string $message): string
    {
        return match (true) {
            str_contains($message, 'access denied') => 'Нет прав на публикацию. Проверьте токен.',
            str_contains($message, 'wiki_disabled') => 'Вики-страницы отключены в настройках группы VK.',
            str_contains($message, 'invalid title') => 'Некорректный заголовок статьи.',
            default => 'Ошибка VK: ' . $message,
        };
    }

    private static function autoPublishPostVk($article): void
    {
        if ($article->link_to_post) {
            // Создаем пост в таблице posts
            $post = Post::create([
                'title' => $article->title,
                'content' => "Анонс статьи: [{$article->short_url}|Читать]",
                'type' => 'vk_article',
                'user_id' => $article->user_id,
                'vk_article_id' => $article->id,
                'is_published' => true,
            ]);

            // Обновляем связь
            $article->update(['post_id' => $post->id]);
        }
    }
}

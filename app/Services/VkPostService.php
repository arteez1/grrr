<?php

namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class VkPostService
{
    private VKApiClient $client;
    private string $accessToken;
    private int $groupId;

    public function __construct()
    {
        $this->client = new VKApiClient();
        $this->accessToken = ApiIntegration::getVkApiToken();
        $this->groupId = abs(config('services.vk.group_id'));
    }

    /**
     * Публикует пост в VK (на стену и/или в статьи)
     */
    public function publishPost(Post $post, bool $toWall = true, bool $toArticles = false): void
    {
        try {
            if ($toWall) {
                $this->publishToWall($post);
            }
            if ($toArticles && $post->type === 'article') {
                $this->publishToArticles($post);
            }
        } catch (\Exception $e) {
            Log::error("VK post publish failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function publishToWall(Post $post): void
    {
        $response = $this->client->wall()->post($this->accessToken, [
            'owner_id' => -$this->groupId,
            'message' => $this->generatePostContent($post),
            'attachments' => $this->uploadImage($post->vk_image)
        ]);

        $post->update(['vk_post_id' => $response['post_id']]);
    }

    private function publishToArticles(Post $post): void
    {
        // Если нужно поддерживать статьи в VK
        $response = $this->client->articles()->add($this->accessToken, [
            'owner_id' => -$this->groupId,
            'title' => $post->title,
            'text' => $post->content
        ]);
    }

    private function generatePostContent(Post $post): string
    {
        $tags = $post->tags->pluck('name')->map(fn ($tag) => "#{$tag}")->implode(' ');
        return $post->short_content ?? substr($post->content, 0, 200)  . "\n\n" . $tags;
    }

    private function uploadImage(string $imagePath): string
    {
        // Реализация загрузки изображения в VK
        $uploadUrl = $this->client->photos()->getWallUploadServer($this->accessToken)['upload_url'];
        $photoData = Http::asMultipart()->attach('file', file_get_contents($imagePath))->post($uploadUrl)->json();
        return $this->client->photos()->saveWallPhoto($this->accessToken, $photoData)[0]['id'];
    }
}

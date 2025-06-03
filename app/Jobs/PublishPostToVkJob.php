<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\VkPostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PublishPostToVkJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Post $post) {}

    public function handle(VkPostService $vkPostService): void
    {
        if ($this->post->is_published_vk && $this->post->publish_at <= now()) {
            $vkPostService->publishPost(
                post: $this->post,
                toWall: true,
                toArticles: false
            );
        }
    }
}

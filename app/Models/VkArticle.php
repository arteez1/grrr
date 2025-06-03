<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'vk_article_id',
        'short_url',
        'vk_post_id',
        'scheduled_at',
        'user_id',
        'is_published',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Post::class, 'vk_article_id');
    }
}

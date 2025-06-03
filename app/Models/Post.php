<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'short_content',
        'main_image',
        'type',
        'vk_article_id',
        'is_published',
        'vk_image',
        'tm_image',
        'is_published_vk',
        'is_published_tm',
        'user_id'
    ];

    // Связь с автором
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь с категориями
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    // Связь с тегами
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function vkArticle()
    {
        return $this->belongsTo(VkArticle::class, 'vk_article_id');
    }
}

<?php

namespace App\Models;

use App\Enums\TagStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'vk_tag_id'
    ];

    // Связь с товарами
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->wherePivot('type', TagStatus::TYPE_PRODUCT);
    }

    // Связь с постами
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->wherePivot('type', TagStatus::TYPE_POST);
    }

    // Аксессоры
    public function getVkTagAttribute(): string
    {
        return $this->vk_tag_id ? "#{$this->name}" : $this->name;
    }
}

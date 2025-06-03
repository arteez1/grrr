<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VkCollection extends Model
{
    protected $fillable = [
        'vk_collection_id',
        'title',
        'is_active',
        'synced_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'synced_at' => 'datetime'
    ];

    protected $dispatchesEvents = [
        'created' => VkCollectionCreated::class,
        'updated' => VkCollectionUpdated::class,
    ];

    // Связь с товарами
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_vk_collection');
    }

    // Аксессор для URL подборки в VK
    public function getVkUrlAttribute(): ?string
    {
        return $this->vk_collection_id
            ? "https://vk.com/market?w=product-{$this->vk_collection_id}"
            : null;
    }
}

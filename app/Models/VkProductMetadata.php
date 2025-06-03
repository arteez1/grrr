<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VkProductMetadata extends Model
{
    protected $fillable = [
        'product_id',
        'vk_product_id',
        'width',
        'height',
        'depth',
        'weight',
        'availability',
        'vk_tags',
        'related_vk_product_ids' // JSON-массив ID сопутствующих товаров в VK
    ];

    protected $casts = [
        'vk_tags' => 'array',
        'availability' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'depth' => 'integer',
        'weight' => 'integer',
        'related_vk_product_ids' => 'array'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function getDimensionsAttribute(): string
    {
        return "{$this->width}x{$this->height}x{$this->depth} мм";
    }
}

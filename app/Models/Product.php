<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'slug','sku', 'description', 'price', 'old_price',
        'stock_quantity', 'is_published', 'is_published_vk', 'is_published_tm',
        'main_image', 'vk_image', 'tm_image',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_published_vk' => 'boolean',
        'is_published_tm' => 'boolean',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category')
            ->where('type', CategoryStatus::CAT_PRODUCT);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function vkMetadata(): HasOne
    {
        return $this->hasOne(VkProductMetadata::class);
    }

    public function vkCollections(): BelongsToMany
    {
        return $this->belongsToMany(VkCollection::class, 'product_vk_collection');
    }

    // Сопутствующие товары
    public function relatedProducts(): HasMany
    {
        return $this->hasMany(RelatedProduct::class, 'product_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'price_at_purchase');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Получить список связанных товаров (через промежуточную таблицу)
    public function getRelatedItems(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products',
            'product_id', 'related_product_id')
            ->withTimestamps();
    }

    // Аксессоры
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, '.', ' ');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        return $this->old_price
            ? round(($this->old_price - $this->price) / $this->old_price * 100)
            : null;
    }
}

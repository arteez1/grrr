<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'parent_id',
        'vk_category_id'
    ];

    protected $casts = [
        'vk_category_id' => 'integer',
        'type' => CategoryStatus::class,
    ];
    // Родительская категория
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Дочерние категории
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Связь с товарами
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,  'product_category')
            ->where('type', CategoryStatus::CAT_PRODUCT);
    }

    // Связь с постами
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class,  'post_category')
            ->whereIn('type', [CategoryStatus::CAT_POST, CategoryStatus::CAT_NEWS]);
    }

    // Связь с VK категориями
    public function vkMappings(): HasMany
    {
        return $this->hasMany(VkCategoryMapping::class);
    }

    // Аксессоры
    public function getFullPathAttribute(): string
    {
        return $this->parent
            ? $this->parent->full_path . ' → ' . $this->name
            : $this->name;
    }
}

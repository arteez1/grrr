<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VkCategoryMapping extends Model
{
    protected $fillable = ['category_id', 'vk_category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

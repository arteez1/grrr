<?php

namespace App\Models;

use App\Services\SpamFilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'reviewable_id',
        'reviewable_type',
        'type', // Тип отзыва (product, post, general)
        'content',
        'rating',
        'is_approved',
        'approved_by',
    ];

    // Полиморфная связь
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Автоматическая проверка на спам при создании
    protected static function booted(): void
    {
        static::creating(function ($review) {
            if (SpamFilterService::isSpam($review->content)) {
                throw new \Exception('Отзыв содержит спам.');
            }
        });
    }
}

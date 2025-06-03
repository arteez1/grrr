<?php

namespace App\Models;

use App\Enums\DiscountStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'type',
        'amount',
        'start_date',
        'end_date',
        'max_uses',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'amount' => 'decimal:2'
    ];

    // Связь с товарами
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_product')->withTimestamps();
    }

    // Проверка активности скидки
    public function isActive(): bool
    {
        return $this->is_active
            && now()->between($this->start_date, $this->end_date)
            && ($this->max_uses === null || $this->used_count < $this->max_uses);
    }
    // Аксессоры
    public function getFormattedAmountAttribute(): string
    {
        return $this->type === DiscountStatus::TYPE_PERCENTAGE
            ? "{$this->amount}%"
            : "{$this->amount} ₽";
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Неактивна';
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return 'Запланирована';
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return 'Истекла';
        }

        return 'Активна';
    }

    // Проверка доступности скидки
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    // Увеличение счетчика использований
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    //Фильтрация скидок в админ-панели
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')
                    ->orWhereRaw('used_count < max_uses');
            });
    }
}

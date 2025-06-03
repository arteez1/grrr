<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'total_amount',
        'discount_id',
        'status',
        'delivery_method',
        'payment_method',
        'vk_order_id',
        'tm_message_id'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Связь со скидкой
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    // Аксессоры
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            OrderStatus::STATUS_PENDING => 'В обработке',
            OrderStatus::STATUS_COMPLETED => 'Завершен',
            OrderStatus::STATUS_CANCELLED => 'Отменен',
            default => $this->status
        };
    }
}

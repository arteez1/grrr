<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VkMessage extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'direction', // 'in' или 'out'
        'payload' // Дополнительные данные (JSON)
    ];

    protected $casts = [
        'payload' => 'json'
    ];

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'user_id', 'vk_user_id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id'); // Если есть order_id в vk_messages
    }
}

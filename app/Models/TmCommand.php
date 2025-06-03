<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TmCommand extends Model
{
    protected $table = 'tm_commands';

    protected $fillable = [
        'bot_id',
        'command',
        'handler_method',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Связь с ботом
    public function bot(): BelongsTo
    {
        return $this->belongsTo(TmBot::class, 'bot_id');
    }
}

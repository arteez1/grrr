<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoResponse extends Model
{
    protected $fillable = ['keyword', 'response_text', 'is_active'];
}

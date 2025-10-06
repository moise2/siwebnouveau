<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebNotification extends Model
{
    use HasFactory;
    protected $table = 'webnotifications';
    protected $fillable = [
        'title', 'message', 'image', 'start_time', 'end_time', 'is_active',
    ];
}

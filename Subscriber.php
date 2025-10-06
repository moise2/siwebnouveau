<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

//use Illuminate\Database\Eloquent\SoftDeletes;


class Subscriber extends Model
{
    //use SoftDeletes;
    use Notifiable;
    

    public static function generateToken()
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (Subscriber::where('verification_code', $token)->exists());
    
        return $token;
    }

    protected $table = 'subscribers';
    protected $fillable = ['email', 'is_active','verification_code','verified'];
    //protected $dates = ['deleted_at'];
    
}
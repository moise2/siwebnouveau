<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Twitter extends Model
{
    use HasFactory;

    protected $fillable = [
        'text', // Ajout de la clé étrangère vers acteur
        'id_twitt',
    ];

    public static function getwitts(){
        return Twitter::all();
    }

    public static function getwitt(){
        return Twitter::orderBy('id','ASC')->first();
    }
}

<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
    ];

    /**
     * The categories that belong to the document.
     */
    public function getToken()
    {
        return Token::first()->token;
    }
}

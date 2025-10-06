<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocaliteProgramme extends Model
{
    use HasFactory;
    protected $fillable = ['programme_id', 'id', 'localite_id'];

    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }
    
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
}

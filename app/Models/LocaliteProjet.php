<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocaliteProjet extends Model
{
    use HasFactory;
    protected $fillable = ['projet_id', 'id', 'localite_id'];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }
}

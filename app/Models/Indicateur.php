<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicateur extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'valeur_reference', 'valeur_cible', 'valeur_actuelle', 'source', 'type', 'projet_id'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
}

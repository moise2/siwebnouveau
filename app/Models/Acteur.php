<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acteur extends Model
{
    protected $fillable = ['nom', 'type', 'utilisateur_id', 'bailleur_id'];

    // Relation avec utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    // Relation avec bailleur
    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class, 'bailleur_id');
    }
}

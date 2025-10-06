<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bailleur extends Model
{
    use HasFactory;

    // Les champs remplissables
    protected $fillable = [
        'nom',
        'image',
    ];

    // Relation avec Acteur
    public function acteurs()
    {
        return $this->morphMany(Acteur::class, 'actable');
    }

    // Autres relations si nécessaire
    // ...
}

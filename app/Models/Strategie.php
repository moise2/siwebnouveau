<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model
class Strategie extends Model
{
    protected $fillable = ['nom', 'description', 'date_debut', 'date_fin'];

    // Relation avec les axes stratégiques
    public function axesStrategiques()
    {
        return $this->hasMany(AxeStrategique::class);
    }

    // Relation avec les projets (si applicable)
    public function projets()
    {
        return $this->hasMany(Projet::class);
    }

    // Relation avec les programmes (si applicable)
    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }
}

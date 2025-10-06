<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    // Les champs remplissables
    protected $fillable = [
        'acteur_id', // Ajout de la clé étrangère vers acteur
        'nom',
        'date_debut',
        'date_fin',
        'taux_execution_physique',
        'taux_execution_financier',
        'budget',
        'priorite_id',
        'axe_strategique_id',
        'etat_projet'
    ];

    // Relation avec Acteur
    public function acteur()
    {
        return $this->belongsTo(Acteur::class);
    }

    // Relation avec Priorite
    public function priorite()
    {
        return $this->belongsTo(Priorite::class);
    }

    // Relation avec AxeStrategique
    public function axeStrategique()
    {
        return $this->belongsTo(AxeStrategique::class);
    }

    // Relation avec Decaissement
    public function decaissements()
    {
        return $this->hasMany(Decaissement::class);
    }

    public function projetlocalite()
    {
        return $this->hasMany(LocaliteProjet::class, 'projet_id')->with('localite');
    }

    public function institution()
    {
        return $this->belongsTo(Acteur::class, 'acteur_id');
    }
}

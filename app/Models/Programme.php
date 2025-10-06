<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
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
        'etat_programme'
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

    // Relation avec Localisation
    public function localisations()
    {
        return $this->hasMany(Localisation::class);
    }


    public function programmelocalite()
    {
        return $this->hasMany(LocaliteProgramme::class, 'programme_id')->with('localite');
    }
    
    public function institution()
    {
        return $this->belongsTo(Acteur::class, 'acteur_id');
    }
}

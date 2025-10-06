<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExecutionPhysiqueFinanciere extends Model
{
    use HasFactory;
    protected $fillable = ['taux_execution_physique', 'taux_execution_financier', 'projet_id', 'programme_id','chiffre_cle_id'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function chiffresCles(): BelongsToMany
    {
        return $this->belongsToMany(
            ChiffreCle::class,
            'chiffre_cle_execution_physique_financiere', // nom de la table pivot
            'execution_physique_financiere_id', // clé étrangère vers ExecutionPhysiqueFinanciere
            'chiffre_cle_id' // clé étrangère vers ChiffreCle
        );
    }
}

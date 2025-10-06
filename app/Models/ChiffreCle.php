<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChiffreCle extends Model
{
    use HasFactory;

    protected $fillable = [
        'debut',
        'fin',
        'taux_physique',
        'taux_financier',
    ];

    /**
     * The categories that belong to the document.
     */



    public static function getChiffresCles()
    {
        return ChiffreCle::all();
    }


    public function executionsPhysiquesFinancieres(): BelongsToMany
{
    return $this->belongsToMany(
        ExecutionPhysiqueFinanciere::class,
        'chiffre_cle_execution_physique_financiere', // Table pivot
        'chiffre_cle_id', // Clé étrangère vers ChiffreCle
        'execution_physique_financiere_id' // Clé étrangère vers ExecutionPhysiqueFinanciere
    );
}

}

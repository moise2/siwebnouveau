<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Synthese extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_programme',
        'taux_physique_programme',
        'taux_financier_programme',
        'total_projet',
        'taux_physique_projet',
        'taux_financier_projet',
    ];

    /**
     * The categories that belong to the document.
     */
    public static function getSynthese()
    {
        return Synthese::first();
    }
}

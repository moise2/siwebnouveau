<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AxeStrategique extends Model
{
    use HasFactory;
    
    protected $table = 'axes_strategiques'; // Définir le nom de la table correct
    protected $fillable = ['libelle', 'strategie_id'];

    public function strategie()
    {
        return $this->belongsTo(Strategie::class);
    }
}

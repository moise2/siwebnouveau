<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reforme extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'objectif_reforme', 'etat_avancement', 'axe_strategique_id','annee', 'id_institution'];

    public function axeStrategique()
    {
        return $this->belongsTo(AxeStrategique::class,'axe_strategique_id');
    }
    
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }
}

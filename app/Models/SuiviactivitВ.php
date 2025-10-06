<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suiviactivité extends Model
{
    use HasFactory;
    protected $fillable = ['activite_id', 'taux_execution_physique_precedent', 'taux_execution_physique_actuel', 'depenses_precedentes', 'depenses_actuelles', 'etat'];
}

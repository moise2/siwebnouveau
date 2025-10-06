<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = ['libelle'];

    // Relation avec utilisateur
    // public function utilisateur()
    // {
    //     return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    // }

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class);
    }


}

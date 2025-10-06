<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localite extends Model
{
    use HasFactory;
    protected $fillable = ['libelle', 'id'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function projets()
    {
        return $this->hasMany(LocaliteProjet::class, 'localite_id')->with('projet');
    }

    public function programmes()
    {
        return $this->hasMany(LocaliteProgramme::class, 'localite_id')->with('programme');
    }
}

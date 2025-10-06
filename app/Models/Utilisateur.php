<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\User as VoyagerUser;

class Utilisateur extends VoyagerUser
{
    use SoftDeletes;
    use HasFactory, Notifiable;

    protected $fillable = [
        'institution',
        'nom',
        'prenoms',
        'sexe',
        'email',
        'contact',
        'password',
        'role_id'
    ];

    /**
     * Relation avec Institution.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function institutions()
    {
        return $this->belongsTo(Institution::class, 'institution');
    }

    // Méthode pour vérifier si l'utilisateur est approuvé
    public function isApproved()
    {
        return $this->approved ?? false; // Assurez-vous d'avoir un champ 'approved' ou une logique similaire
    }
}

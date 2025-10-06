<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decaissement extends Model
{
    use HasFactory;
    protected $fillable = ['bailleur_id', 'montant', 'type_financement', 'date_decaissement'];

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }
}

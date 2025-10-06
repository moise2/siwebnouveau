<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrioritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crée la table 'priorites' avec une colonne 'id' et 'libelle'
        Schema::create('priorites', function (Blueprint $table) {
            $table->id(); // Colonne 'id' auto-incrémentée
            $table->string('libelle'); // Colonne pour le libellé de la priorité
            $table->timestamps(); // Colonne pour 'created_at' et 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprime la table 'priorites' si elle existe
        Schema::dropIfExists('priorites');
    }
}

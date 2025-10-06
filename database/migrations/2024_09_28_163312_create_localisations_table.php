<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localisations', function (Blueprint $table) {
            $table->id(); // Colonne id auto-incrémentée
            $table->string('nom'); // Nom de la localisation
            $table->unsignedBigInteger('projet_id')->nullable(); // Projet associé (peut être null en cas de suppression de projet)
            $table->timestamps(); // 'created_at' et 'updated_at'

            // Définition de la clé étrangère avec cascade on delete pour éviter les problèmes de références brisées
            $table->foreign('projet_id')
                ->references('id')
                ->on('projets')
                ->onDelete('cascade'); // Supprime les localisations associées quand un projet est supprimé
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprime la table 'localisations' si elle existe
        Schema::dropIfExists('localisations');
    }
}

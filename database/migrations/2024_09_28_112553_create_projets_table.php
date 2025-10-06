<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetsTable extends Migration
{
    public function up()
    {
        // Vérifier si la table 'projets' existe déjà
        if (!Schema::hasTable('projets')) {
            Schema::create('projets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('acteur_id'); // Clé étrangère vers acteurs
                $table->string('nom');
                $table->date('date_debut');
                $table->date('date_fin');
                $table->double('taux_execution_physique', 8, 2);
                $table->double('taux_execution_financier', 8, 2);
                $table->double('budget', 8, 2);
                $table->unsignedBigInteger('priorite_id'); // Clé étrangère vers priorites
                $table->unsignedBigInteger('axe_strategique_id'); // Clé étrangère vers axes_strategiques
                $table->string('etat_projet');
                $table->timestamps();

                // Clés étrangères
                $table->foreign('acteur_id')->references('id')->on('acteurs')->onDelete('cascade');
                $table->foreign('priorite_id')->references('id')->on('priorites')->onDelete('cascade');
                $table->foreign('axe_strategique_id')->references('id')->on('axes_strategiques')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('projets');
    }
}

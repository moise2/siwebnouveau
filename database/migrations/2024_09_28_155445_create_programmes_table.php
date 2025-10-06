<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table 'programmes' existe déjà
        if (!Schema::hasTable('programmes')) {
            Schema::create('programmes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('acteur_id'); // Clé étrangère vers acteurs
                $table->string('nom')->nullable();
                $table->date('date_debut')->nullable();
                $table->date('date_fin')->nullable();
                $table->double('taux_execution_physique', 8, 2)->nullable();
                $table->double('taux_execution_financier', 8, 2)->nullable();
                $table->double('budget', 8, 2)->nullable();
                $table->unsignedBigInteger('priorite_id')->nullable(); // Clé étrangère vers priorites
                $table->unsignedBigInteger('axe_strategique_id')->nullable(); // Clé étrangère vers axes_strategiques
                $table->string('etat_programme')->nullable();
                $table->timestamps();

                // Clés étrangères
                $table->foreign('acteur_id')->references('id')->on('acteurs')->onDelete('cascade');
                $table->foreign('priorite_id')->references('id')->on('priorites')->onDelete('cascade'); // Assurez-vous que la table priorites existe
                $table->foreign('axe_strategique_id')->references('id')->on('axes_strategiques')->onDelete('cascade'); // Assurez-vous que la table axes_strategiques existe
            }); // Cette accolade fermera le bloc de création de la table
        } // Cette accolade fermera le bloc if
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmes');
    }
}

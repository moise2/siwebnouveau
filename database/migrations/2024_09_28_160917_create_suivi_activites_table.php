<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuiviActivitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivi_activites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activite_id');
            $table->float('taux_execution_physique_precedent');
            $table->float('taux_execution_physique_actuel');
            $table->float('depenses_precedentes');
            $table->float('depenses_actuelles');
            $table->string('etat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suivi_activites');
    }
}

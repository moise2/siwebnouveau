<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExecutionPhysiqueFinancieresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table n'existe pas déjà
        if (!Schema::hasTable('execution_physique_financieres')) {
            Schema::create('execution_physique_financieres', function (Blueprint $table) {
                $table->id();
                $table->float('taux_execution_physique');
                $table->float('taux_execution_financier');
                $table->unsignedBigInteger('projet_id');
                $table->unsignedBigInteger('programme_id')->nullable();
                $table->unsignedBigInteger('chiffre_cle_id');
                $table->timestamps();

                $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
                $table->foreign('chiffre_cle_id')->references('id')->on('chiffre_cles')->onDelete('set null');
                $table->foreign('programme_id')->references('id')->on('programmes')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('execution_physique_financieres');
    }
}

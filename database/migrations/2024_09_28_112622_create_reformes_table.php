<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReformesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('reformes')) {
            Schema::create('reformes', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('ministere');
                $table->text('objectif_reforme');
                $table->string('etat_avancement');
                $table->unsignedBigInteger('axe_strategique_id');
                $table->timestamps();

                // Clé étrangère avec suppression en cascade
                $table->foreign('axe_strategique_id')
                    ->references('id')
                    ->on('axes_strategiques')
                    ->onDelete('cascade'); // Ajout de cascade pour éviter les erreurs de référence
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
        Schema::dropIfExists('reformes');
    }
}

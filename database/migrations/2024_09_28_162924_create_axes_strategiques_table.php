<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAxesStrategiquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifiez d'abord que la table 'strategies' existe
        if (!Schema::hasTable('strategies')) {
            throw new Exception('La table strategies n\'existe pas encore. Veuillez la créer avant de migrer les axes_strategiques.');
        }

        Schema::create('axe_strategiques', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->nullable();
            $table->unsignedBigInteger('strategie_id')->nullable();
            $table->timestamps();

            // Ajout de la clé étrangère avec stratégie de suppression en cascade
            $table->foreign('strategie_id')
                ->references('id')
                ->on('strategies')
                ->onDelete('cascade'); // ou 'restrict' selon le comportement souhaité
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('axes_strategiques');
    }
}

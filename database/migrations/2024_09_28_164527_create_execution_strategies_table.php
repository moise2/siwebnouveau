<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExecutionStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migration
        Schema::create('execution_strategies', function (Blueprint $table) {
            $table->id();
            $table->string('nom');  // Nom de la stratégie
            $table->string('description')->nullable();  // Description de la stratégie
            $table->date('date_debut');  // Date de début de la stratégie
            $table->date('date_fin');  // Date de fin de la stratégie
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
        Schema::dropIfExists('execution_strategies');
    }
}

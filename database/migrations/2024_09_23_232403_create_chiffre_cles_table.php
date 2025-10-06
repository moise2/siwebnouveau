<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChiffreClesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chiffre_cles', function (Blueprint $table) {
            $table->id();
            $table->date('debut')->nullable();
            $table->date('fin')->nullable();
            $table->double('taux_physique')->nullable();
            $table->double('taux_financier')->nullable();
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
        Schema::dropIfExists('chiffre_cles');
    }
}

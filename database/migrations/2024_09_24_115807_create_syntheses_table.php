<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSynthesesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syntheses', function (Blueprint $table) {
            $table->id();
            $table->integer('total_programme')->nullable();
            $table->double('taux_physique_programme')->nullable();
            $table->double('taux_financier_programme')->nullable();
            $table->integer('total_projet')->nullable();
            $table->double('taux_physique_projet')->nullable();
            $table->double('taux_financier_projet')->nullable();
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
        Schema::dropIfExists('syntheses');
    }
}

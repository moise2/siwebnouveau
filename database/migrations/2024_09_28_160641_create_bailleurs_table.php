<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBailleursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifiez si la table 'bailleurs' n'existe pas avant de la créer
        if (!Schema::hasTable('bailleurs')) {
            Schema::create('bailleurs', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('image');
                //$table->float('contribution_totale'); // Changez float() par double() si vous avez besoin de plus de précision
                $table->timestamps();
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
        Schema::dropIfExists('bailleurs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('decaissements')) {
            Schema::create('decaissements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bailleur_id');
                $table->float('montant');
                $table->string('type_financement');
                $table->date('date_decaissement');
                $table->timestamps();

                $table->foreign('bailleur_id')->references('id')->on('bailleurs');
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
        Schema::dropIfExists('decaissements');
    }
}

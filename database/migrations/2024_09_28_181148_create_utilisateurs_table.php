<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilisateursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('nom');
            $table->string('prenoms');
            $table->enum('sexe', ['Homme', 'Femme']);
            $table->string('email')->unique();
            $table->string('contact')->nullable();
            $table->string('password');
            $table->boolean('approved')->default(false); // Ajout du champ approved
            $table->timestamps();

            // Ajout des clés étrangères
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('institution_id')->nullable();
            
            $table->timestamps();

            // Définition des contraintes de clé étrangère
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utilisateurs');
    }
}

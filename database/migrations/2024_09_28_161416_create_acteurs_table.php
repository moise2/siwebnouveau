<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActeursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifie si la table 'acteurs' existe déjà avant de la créer
        if (!Schema::hasTable('acteurs')) {
            Schema::create('acteurs', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('type'); // Peut-être 'utilisateur', 'bailleur', etc.

                // Clés étrangères pour lier aux tables utilisateurs et bailleurs
                $table->unsignedBigInteger('utilisateur_id')->nullable();
                $table->unsignedBigInteger('bailleur_id')->nullable();

                // Clés étrangères pour programmes et projets
                $table->unsignedBigInteger('programme_id')->nullable();
                $table->unsignedBigInteger('projet_id')->nullable();

                $table->timestamps();
            });

            // Ajout des clés étrangères avec vérification des tables après la création
            Schema::table('acteurs', function (Blueprint $table) {
                if (Schema::hasTable('utilisateurs')) {
                    $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
                }

                if (Schema::hasTable('bailleurs')) {
                    $table->foreign('bailleur_id')->references('id')->on('bailleurs')->onDelete('cascade');
                }

                if (Schema::hasTable('programmes')) {
                    $table->foreign('programme_id')->references('id')->on('programmes')->onDelete('cascade');
                }

                if (Schema::hasTable('projets')) {
                    $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
                }
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
        Schema::dropIfExists('acteurs');
    }
}

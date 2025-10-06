<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table 'users' n'existe pas déjà
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Assurez-vous que cette ligne est présente
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // Si la table existe, vérifiez les colonnes
            $this->checkAndAddColumnsIfNeeded();
        }
    }

    /**
     * Vérifier et ajouter des colonnes si elles n'existent pas.
     *
     * @return void
     */
    protected function checkAndAddColumnsIfNeeded()
    {
        $columns = Schema::getColumnListing('users');

        // Vérifiez chaque colonne nécessaire et ajoutez-la si elle n'existe pas
        if (!in_array('name', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->after('id');
            });
        }

        if (!in_array('email', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->unique()->after('name');
            });
        }

        if (!in_array('email_verified_at', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            });
        }

        if (!in_array('password', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->after('email_verified_at');
            });
        }

        if (!in_array('remember_token', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->rememberToken()->after('password');
            });
        }

        if (!in_array('created_at', $columns)) {
            Schema::table('users', function (Blueprint $table) {
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
        // Supprimer la table 'users' si elle existe
        Schema::dropIfExists('users');
    }
}

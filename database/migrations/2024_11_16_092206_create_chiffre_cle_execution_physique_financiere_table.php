<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChiffreCleExecutionPhysiqueFinanciereTable extends Migration
{
    public function up()
    {
        // Check if the table already exists
        if (!Schema::hasTable('chiffre_cle_execution_physique_financiere')) {
            Schema::create('chiffre_cle_execution_physique_financiere', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chiffre_cle_id')->constrained()->onDelete('cascade');
                $table->foreignId('execution_physique_financiere_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Check if the table exists before attempting to drop it
        if (Schema::hasTable('chiffre_cle_execution_physique_financiere')) {
            Schema::dropIfExists('chiffre_cle_execution_physique_financiere');
        }
    }
}

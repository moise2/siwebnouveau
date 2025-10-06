<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wp_post_id')->nullable(); // ID du document sur WordPress
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->integer('file_size');
            $table->string('file_type');
            $table->year('year');
            $table->enum('status', ['PUBLISHED', 'DRAFT', 'PENDING', 'ARCHIVED'])->default('DRAFT');
            $table->enum('access_type', ['PUBLIC', 'RESTRICTED', 'VIEW_ONLY'])->default('PUBLIC');
            $table->dateTime('expiration_date')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}

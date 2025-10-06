<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryDocumentTable extends Migration
{
    public function up()
    {
        Schema::create('category_document', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_category_id');
            $table->unsignedBigInteger('document_id');
            $table->timestamps();

            $table->foreign('document_category_id')->references('id')->on('document_categories')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_document');
    }
}

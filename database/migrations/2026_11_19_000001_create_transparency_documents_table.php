<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransparencyDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('transparency_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('title');
            $table->unsignedSmallInteger('year');
            $table->string('period', 80)->nullable();
            $table->string('version', 80)->nullable();
            $table->string('budget_code', 30)->nullable();
            $table->string('source', 255);
            $table->string('pdf_path')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
            $table->index(['page_id', 'published_at', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transparency_documents');
    }
}

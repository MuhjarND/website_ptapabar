<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfFieldsToArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('pdf_file')->nullable()->after('image');
            $table->string('pdf_url', 500)->nullable()->after('pdf_file');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['pdf_file', 'pdf_url']);
        });
    }
}

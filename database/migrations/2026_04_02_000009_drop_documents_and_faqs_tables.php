<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropDocumentsAndFaqsTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('faqs');
    }

    public function down()
    {
        //
    }
}

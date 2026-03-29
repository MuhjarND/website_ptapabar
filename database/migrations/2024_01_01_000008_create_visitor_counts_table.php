<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorCountsTable extends Migration
{
    public function up()
    {
        Schema::create('visitor_counts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('visit_date')->unique();
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_counts');
    }
}

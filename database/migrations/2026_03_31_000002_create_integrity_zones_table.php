<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrityZonesTable extends Migration
{
    public function up()
    {
        Schema::create('integrity_zones', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('url', 500);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('integrity_zones');
    }
}

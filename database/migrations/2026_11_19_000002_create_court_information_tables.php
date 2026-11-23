<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourtInformationTables extends Migration
{
    public function up()
    {
        Schema::create('court_hearings', function (Blueprint $table) {
            $table->id();
            $table->date('hearing_date');
            $table->time('hearing_time')->nullable();
            $table->string('title', 160);
            $table->string('location', 160)->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['hearing_date', 'is_published']);
        });

        Schema::create('case_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('period')->unique();
            $table->unsignedInteger('opening');
            $table->unsignedInteger('received');
            $table->unsignedInteger('decided');
            $table->unsignedInteger('closing');
            $table->string('source', 160);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('case_statistics');
        Schema::dropIfExists('court_hearings');
    }
}

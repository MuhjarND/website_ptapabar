<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('site_applications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('group_type', 50);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group_type', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_applications');
    }
}

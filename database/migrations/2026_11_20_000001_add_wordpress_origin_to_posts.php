<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWordpressOriginToPosts extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique();
            $table->string('wordpress_cover_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique(['wordpress_id']);
            $table->dropColumn(['wordpress_id', 'wordpress_cover_path']);
        });
    }
}

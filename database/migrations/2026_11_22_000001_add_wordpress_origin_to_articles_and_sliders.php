<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique();
            $table->string('wordpress_cover_path')->nullable();
        });
        Schema::table('sliders', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique();
            $table->string('wordpress_cover_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropUnique(['wordpress_id']);
            $table->dropColumn(['wordpress_id', 'wordpress_cover_path']);
        });
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropUnique(['wordpress_id']);
            $table->dropColumn(['wordpress_id', 'wordpress_cover_path']);
        });
    }
};

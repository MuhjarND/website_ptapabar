<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreatePostCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('post_category_id')->nullable()->after('category');
            $table->foreign('post_category_id')->references('id')->on('post_categories')->onDelete('set null');
        });

        $defaults = ['Rapat', 'Sosialisasi', 'Rakor', 'Bimtek', 'Kunjungan', 'Lainnya'];

        foreach ($defaults as $index => $name) {
            DB::table('post_categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'order' => $index,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['post_category_id']);
            $table->dropColumn('post_category_id');
        });

        Schema::dropIfExists('post_categories');
    }
}

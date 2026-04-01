<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateGalleryCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->unsignedBigInteger('gallery_category_id')->nullable()->after('type');
            $table->foreign('gallery_category_id')->references('id')->on('gallery_categories')->onDelete('set null');
        });

        $defaults = ['Kegiatan', 'Layanan', 'Sosialisasi', 'Lainnya'];

        foreach ($defaults as $index => $name) {
            DB::table('gallery_categories')->insert([
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
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropForeign(['gallery_category_id']);
            $table->dropColumn('gallery_category_id');
        });

        Schema::dropIfExists('gallery_categories');
    }
}

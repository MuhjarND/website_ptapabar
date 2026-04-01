<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakePageSlugsUnique extends Migration
{
    public function up()
    {
        $pages = DB::table('pages')->select('id', 'title', 'slug')->orderBy('id')->get();
        $usedSlugs = [];

        foreach ($pages as $page) {
            $baseSlug = Str::slug($page->slug ?: $page->title);
            $slug = $baseSlug !== '' ? $baseSlug : 'halaman';
            $originalSlug = $slug;
            $counter = 2;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            if ($page->slug !== $slug) {
                DB::table('pages')->where('id', $page->id)->update(['slug' => $slug]);
            }

            $usedSlugs[] = $slug;
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
    }
}

<?php

use App\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewsScopeToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('news_scope')->nullable()->after('post_category_id');
        });

        DB::table('posts')
            ->where('category', 'berita')
            ->update(['news_scope' => Post::NEWS_SCOPE_TERKINI]);
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('news_scope');
        });
    }
}

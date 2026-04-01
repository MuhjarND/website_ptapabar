<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnnouncementCategoryToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('announcement_category', 50)->nullable()->after('post_category_id');
            $table->index('announcement_category');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['announcement_category']);
            $table->dropColumn('announcement_category');
        });
    }
}

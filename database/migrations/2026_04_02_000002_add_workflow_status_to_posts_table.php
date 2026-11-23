<?php

use App\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWorkflowStatusToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('workflow_status', 32)->default(Post::STATUS_PUBLISHED)->after('announcement_category');
            $table->timestamp('published_at')->nullable()->after('workflow_status');
        });

        DB::table('posts')
            ->where('is_published', true)
            ->update([
                'workflow_status' => Post::STATUS_PUBLISHED,
                'published_at' => DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)'),
            ]);

        DB::table('posts')
            ->where('is_published', false)
            ->update([
                'workflow_status' => Post::STATUS_DRAFT,
            ]);
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['workflow_status', 'published_at']);
        });
    }
}

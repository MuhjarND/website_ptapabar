<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('post_categories')->insertOrIgnore([
            'name' => 'Agenda Pimpinan',
            'slug' => 'agenda-pimpinan',
            'order' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Keep the category and its assignments to published news.
    }
};

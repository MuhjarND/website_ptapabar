<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_key')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 20)->default('unknown');
            $table->string('country_code', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->unsignedInteger('total_requests')->default(0);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index('last_seen_at');
            $table->index('device_type');
            $table->index('country_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_sessions');
    }
}

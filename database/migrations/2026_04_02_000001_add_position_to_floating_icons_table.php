<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionToFloatingIconsTable extends Migration
{
    public function up()
    {
        Schema::table('floating_icons', function (Blueprint $table) {
            $table->string('position', 32)->default('bottom_right')->after('url');
        });
    }

    public function down()
    {
        Schema::table('floating_icons', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}

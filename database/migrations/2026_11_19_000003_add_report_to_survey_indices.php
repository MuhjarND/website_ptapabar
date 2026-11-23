<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportToSurveyIndices extends Migration
{
    public function up()
    {
        Schema::table('survey_indices', function (Blueprint $table) {
            $table->string('period', 80)->nullable();
            $table->string('report_url', 2048)->nullable();
        });
    }

    public function down()
    {
        Schema::table('survey_indices', function (Blueprint $table) {
            $table->dropColumn(['period', 'report_url']);
        });
    }
}

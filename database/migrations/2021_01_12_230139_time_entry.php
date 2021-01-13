<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TimeEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->string('target_duration')->nullable();
            $table->string('type')->default('stopwatch');
            $table->string('iso_duration')->nullable();
            $table->string('disturbance')->nullable();
            $table->string('end_Reason')->nullable();
            $table->boolean('promodoro_completed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn('target_duration');
            $table->dropColumn('type');
            $table->dropColumn('iso_duration');
            $table->dropColumn('disturbance');
            $table->dropColumn('end_reason');
            $table->dropColumn('promodoro_completed');
        });
    }
}

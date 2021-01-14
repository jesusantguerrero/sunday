<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBoard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->bigInteger('board_type_id')->default(1);
            $table->bigInteger('board_template_id')->default(1);
            $table->bigInteger('last_stage_id')->nullable();
            $table->bigInteger('last_view_id')->default(1);
            $table->text('last_filter')->nullable();
            $table->boolean('pinned')->default(0);
            $table->string('color')->nullable();
            $table->text('cover')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            //
        });
    }
}

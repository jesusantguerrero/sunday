<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('neatlancer_id')->nullable();
            $table->string('neatlancer_token')->nullable();
            $table->string('neatlancer_refresh_token')->nullable();
            $table->string('password')->nullable()->change();
        });
    }
};

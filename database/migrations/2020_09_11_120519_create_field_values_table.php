<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id');
            $table->unsignedBigInteger('entity_id');
            $table->string('resource');
            $table->string('field_name');
            $table->string('value')->nullable();
            $table->date('date_value')->nullable();
            $table->decimal('decimal_value')->nullable();
            $table->boolean('check_value')->nullable();
            $table->text('text_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_values');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_id');
            $table->bigInteger('user_id');
            $table->bigInteger('resource_id')->nullable();
            $table->bigInteger('resource_type_id')->nullable();
            $table->integer('number');
            $table->date('date');

            // header
            $table->string('concept', 50);
            $table->string('description', 200);

            // footer
            $table->text('notes')->nullable();

            // totals
            $table->decimal('total', 11, 2)->default(0.00);
            $table->enum('status', ['draft','verified', 'canceled'])->default('draft');
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
        Schema::dropIfExists('transactions');
    }
}

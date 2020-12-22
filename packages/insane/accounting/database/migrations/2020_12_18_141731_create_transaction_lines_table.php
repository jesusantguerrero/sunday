<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_lines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('team_id');
            $table->bigInteger('transaction_id');

            // content
            $table->bigInteger('account_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->integer('type')->default(1)->comment("1 debit, -1 credit");
            $table->text('concept', 300);
            $table->decimal('amount', 11, 2)->default(0.00);
            $table->integer('index')->nullable();
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
        Schema::dropIfExists('transaction_lines');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashenvoyTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashenvoy_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ce_transref')->nullable();
            $table->string('transaction_id')->nullable();
            $table->float('ce_amount', 15, 4)->nullable();
            $table->string('ce_type')->nullable();
            $table->float('amount', 15, 4)->default(0.00);
            $table->json('items');
            $table->string('ce_customerid')->nullable();
            $table->text('ce_memo')->nullable();
            $table->string('status')->default('Pending');
            $table->string('response_code')->nullable();
            $table->string('response_description')->default('Pending');
            $table->timestamps();
            $table->softDeletes();

            $table->engine = 'InnoDB';
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cashenvoy_transactions');
    }
}

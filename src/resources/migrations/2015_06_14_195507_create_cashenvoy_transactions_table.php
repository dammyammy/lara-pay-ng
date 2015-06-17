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
//            $table->string('merchant_ref')->nullable();
//            $table->string('v_transaction_id')->nullable();
//            $table->float('v_total', 15, 4)->nullable();
//            $table->float('v_total_paid', 15, 4)->nullable();
//            $table->float('v_total_credited', 15, 4)->nullable();
//            $table->float('v_extra_charges', 10, 4)->nullable();
//            $table->string('v_pay_method')->nullable();
//            $table->string('v_fund_maturity')->nullable();
//            $table->string('v_email')->nullable();
//            $table->float('v_merchant_charges', 10, 4)->nullable();
//            $table->float('v_process_duration', 6, 4)->nullable();
//            $table->float('total', 15, 4);
//            $table->json('items');
//            $table->string('store_id')->nullable();
//            $table->string('payer_id')->nullable();
//            $table->boolean('recurrent')->default(0);
//            $table->integer('interval')->nullable();
//            $table->text('memo')->nullable();
//            $table->string('referrer')->nullable();
//            $table->string('status')->default('Pending');
//            $table->timestamp('paid_at')->nullable();
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGtpayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gtpay_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gtpay_tranx_id')->nullable()->unique();
            $table->string('r_gtpay_tranx_id')->nullable();
            $table->string('gtpay_merchant_ref')->nullable();
            $table->float('gtpay_tranx_amt', 15, 4)->nullable();
            $table->string('gtpay_tranx_curr')->nullable();
            $table->string('gtpay_cust_id')->nullable();
            $table->string('gtpay_echo_data')->nullable();
            $table->text('gtpay_cust_name')->nullable();
            $table->float('r_gtpay_amount', 15, 4)->nullable();
            $table->json('items');
            $table->text('gtpay_tranx_memo')->nullable();
            $table->string('gtpay_response_code')->nullable();
            $table->string('gtpay_response_description')->default('Pending');
            $table->string('gtpay_tranx_status_code')->nullable();
            $table->string('gtpay_tranx_status_msg')->nullable();
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
        Schema::drop('gtpay_transactions');
    }
}

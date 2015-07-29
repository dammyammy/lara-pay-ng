<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebpayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webpay_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
//            $table->string('gtpay_tranx_id')->nullable();
//            $table->string('r_gtpay_tranx_id')->nullable();
//            $table->float('gtpay_tranx_amt', 15, 4)->nullable();
//            $table->enum('gtpay_tranx_curr', [556,884])->nullable();
//            $table->string('gtpay_cust_id')->nullable();
//            $table->string('gtpay_echo_data')->nullable();
//            $table->text('gtpay_cust_name')->nullable();
//            $table->string('gtpay_tranx_hash')->nullable();
//            $table->string('gtpay_merchant_reference')->nullable();
//            $table->string('gtpay_merchant_id')->nullable();
//            $table->float('r_gtpay_amount', 15, 4);
//            $table->json('items');
//            $table->text('gtpay_tranx_memo')->nullable();
//            $table->string('gtpay_response_code')->nullable();
//            $table->string('gtpay_response_description')->default('Pending');
//            $table->string('gtpay_tranx_status_code')->nullable();
//            $table->string('gtpay_tranx_status_msg')->nullable();
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
        Schema::drop('webpay_transactions');
    }
}

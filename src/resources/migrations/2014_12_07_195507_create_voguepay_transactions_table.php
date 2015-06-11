<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoguepayTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        Schema::create('voguepay_transactions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('merchant_ref');
            $table->string('transaction_id');
            $table->float('total');
            $table->json('items');
            $table->string('store_id')->nullable();
            $table->string('payer_id')->nullable();
            $table->boolean('recurrent')->default(0);
            $table->integer('interval')->nullable();
            $table->string('email')->nullable();
            $table->text('memo')->nullable();
            $table->float('received_total')->nullable();
            $table->string('referrer')->nullable();
            $table->string('status')->default('Pending');
            $table->string('method')->nullable();
            $table->timestamp('paid_at')->nullable();
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
        Schema::drop('voguepay_transactions');
    }


}

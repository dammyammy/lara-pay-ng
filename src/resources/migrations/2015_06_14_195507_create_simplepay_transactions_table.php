<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSimplepayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simplepay_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customid')->nullable();
            $table->string('s_customid')->nullable();
            $table->string('s_transaction_id')->nullable();
            $table->float('s_total', 15, 4)->nullable();
            $table->float('s_fees', 15, 4)->nullable();
            $table->float('commission_amount', 15, 4)->nullable();
            $table->string('s_pid')->nullable();
            $table->string('s_pname')->nullable();
            $table->float('price', 15, 4);
            $table->float('setup', 15, 4);
            $table->float('tax', 15, 4);
            $table->float('shipping', 15, 4);
            $table->json('items');
            $table->string('s_buyer')->nullable();
            $table->string('payer_id')->nullable();
            $table->boolean('escrow')->default(false);
            $table->boolean('freeclient')->default(true);
            $table->boolean('nocards')->default(false);
            $table->boolean('giftcards')->default(false);
            $table->boolean('chargeforcards')->default(true);
            $table->integer('trialperiod')->nullable(); // Recurrent
            $table->integer('period')->nullable(); // Recurrent
            $table->text('comments')->nullable();
            $table->string('action')->nullable();
            $table->string('referrer')->nullable();
            $table->string('status')->default('Pending');
            $table->string('status_code')->nullable();
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
        Schema::drop('simplepay_transactions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('branch_id', 26)->index();
            $table->char('cashier_id', 26)->index();
            $table->char('prepared_by_id', 26)->index()->nullable();
            $table->char('status', 20);
            $table->char('payer_id', 26)->index()->nullable();
            $table->char('payerType', 20);
            $table->char('payee_id', 26)->index()->nullable();
            $table->char('payeeType', 20);
            $table->char('payment_item_id', 26)->index();
            $table->decimal('billAmount', 10, 2);
            $table->decimal('paidAmount', 10, 2);
            $table->char('bill_currency_id', 26)->index();
            $table->char('paid_currency_id', 26)->index();
            $table->char('exchange_rate_id', 26)->index()->nullable();
            $table->string('comment')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('payments');
    }
}

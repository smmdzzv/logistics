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
            $table->char('payer_account_in_bill_currency_id', 26)->index()->nullable();
            $table->char('payer_account_in_second_currency_id', 26)->index()->nullable();
            $table->char('payer_type', 20)->nullable();
            $table->char('payee_id', 26)->index()->nullable();
            $table->char('payee_account_in_bill_currency_id', 26)->index()->nullable();
            $table->char('payee_account_in_second_currency_id', 26)->index()->nullable();
            $table->char('payee_type', 20)->nullable();
            $table->char('payment_item_id', 26)->index();
            $table->char('related_payment_id', 26)->index()->nullable();
            $table->decimal('billAmount', 10, 2);
            $table->decimal('paidAmountInBillCurrency', 10, 2);
            $table->decimal('paidAmountInSecondCurrency', 10, 2);
            $table->char('bill_currency_id', 26)->index();
            $table->char('second_paid_currency_id', 26)->index()->nullable();
            $table->char('exchange_rate_id', 26)->index()->nullable();
            $table->string('comment')->nullable();

            $table->decimal('billAmountInTjs', 10, 2)->nullable();
            $table->char('exchange_rate_to_tjs', 26)->nullable();
            $table->decimal('clientDebt', 10, 2)->nullable();
            $table->integer('placesLeft')->nullable();
            $table->integer('number')->nullable();

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

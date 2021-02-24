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
            $table->uuid('id')->primary();
            $table->uuid('branch_id')->index();
//            $table->uuid('cashier_id')->index();
//            $table->uuid('prepared_by_id')->index()->nullable();
            $table->char('status', 20);
            $table->uuid('payer_id')->index()->nullable();
            $table->uuid('payer_account_in_bill_currency_id')->index()->nullable();
            $table->uuid('payer_account_in_second_currency_id')->index()->nullable();
            $table->char('payer_type', 20)->nullable();
            $table->uuid('payee_id')->index()->nullable();
            $table->uuid('payee_account_in_bill_currency_id')->index()->nullable();
            $table->uuid('payee_account_in_second_currency_id')->index()->nullable();
            $table->char('payee_type', 20)->nullable();
            $table->uuid('payment_item_id')->index();
            $table->uuid('related_payment_id')->index()->nullable();
            $table->decimal('billAmount', 10, 2);
            $table->decimal('paidAmountInBillCurrency', 10, 2);
            $table->decimal('paidAmountInSecondCurrency', 10, 2);
            $table->uuid('bill_currency_id')->index();
            $table->uuid('second_paid_currency_id')->index()->nullable();
            $table->uuid('exchange_rate_id')->index()->nullable();
            $table->string('comment')->nullable();

            $table->decimal('billAmountInTjs', 10, 2)->nullable();
            $table->uuid('exchange_rate_to_tjs')->nullable();
            $table->decimal('clientDebt', 10, 2)->nullable();
            $table->integer('placesLeft')->nullable();
            $table->integer('number')->nullable();

            $table->uuid('client_items_selection_id')->index()->nullable();
            $table->decimal('discount', 10, 2)->nullable();

            $table->boolean('approved')->nullable();

            $table->userStamp();
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

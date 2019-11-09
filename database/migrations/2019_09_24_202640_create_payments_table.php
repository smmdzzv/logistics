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
            $table->char('branchId', 26);
            $table->char('cashierId', 26);
            $table->char('currencyId', 26);
            $table->char('payerId', 26)->nullable();
            $table->char('paymentItemId', 26);
            $table->char('exchangeId', 26)->nullable();
            $table->char('accountFromId', 26)->nullable();
            $table->char('accountToId', 26)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('comment')->nullable();
            $table->char('status', 15);
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

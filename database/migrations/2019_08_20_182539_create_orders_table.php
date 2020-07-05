<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->char('id', 26)->primary();;
            $table->char('owner_id', 26)->index();
//            $table->char('paymentId', 26)->nullable()->unique();
//            $table->char('registeredById', 26);
            $table->char('branch_id', 26);
            $table->string('status')->nullable();
            $table->decimal('totalCount', 10, 2);
            $table->decimal('totalWeight', 10, 3);
            $table->decimal('totalCubage', 10, 3);
            $table->decimal('totalPrice', 10, 2);
            $table->decimal('totalDiscount', 10, 2);
            $table->userStamp();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

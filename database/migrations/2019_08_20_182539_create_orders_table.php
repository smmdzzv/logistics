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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner');
            $table->unsignedBigInteger('registeredBy');
            $table->unsignedBigInteger('branch');
            $table->string('status')->default('accepted');
            $table->double('totalCount');
            $table->double('totalWeight');
            $table->double('totalCubage');
            $table->double('totalPrice');
            $table->double('totalDiscount');
            $table->timestamps();

            $table->index('owner');
            $table->index('registeredBy');
            $table->index('branch');
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

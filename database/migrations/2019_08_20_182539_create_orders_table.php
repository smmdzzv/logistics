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
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('registeredBy');
            $table->string('status')->default('accepted');
            $table->double('totalCount');
            $table->double('totalWeight');
            $table->double('totalCubage');
            $table->double('totalPrice');
            $table->double('totalDiscount');
            $table->timestamps();

            $table->index('client_id');
            $table->index('registeredBy');
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

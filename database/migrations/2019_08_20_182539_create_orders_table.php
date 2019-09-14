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
            $table->char('owner', 26);
            $table->char('registeredBy', 26);
            $table->char('branch', 26);
            $table->string('status')->default('accepted');
            $table->double('totalCount');
            $table->double('totalWeight');
            $table->double('totalCubage');
            $table->double('totalPrice');
            $table->double('totalDiscount');
            $table->timestamps();

            $table->index('owner');
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

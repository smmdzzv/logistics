<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tariff_price_history_id');
//            $table->integer('placesCount');
            $table->double('totalCubage');
            $table->double('totalWeight');
            $table->double('weightPerCube');
            $table->double('pricePerItem');
            $table->double('totalPrice');
            $table->double('discountPerCube');
            $table->double('totalDiscount');
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
        Schema::dropIfExists('billing_infos');
    }
}

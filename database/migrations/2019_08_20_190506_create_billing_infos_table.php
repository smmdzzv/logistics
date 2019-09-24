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
            $table->char('id', 26)->primary();;
            $table->char('tariff_price_history_id', 26);
            $table->char('stored_item_id', 26);
//            $table->integer('placesCount');
            $table->double('totalCubage');
            $table->double('totalWeight');
            $table->double('weightPerCube');
            $table->double('pricePerItem');
            $table->double('totalPrice', 10, 5);
            $table->double('discountPerCube');
            $table->double('totalDiscount', 10, 5);
            $table->timestamps();

            $table->index('stored_item_id');
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

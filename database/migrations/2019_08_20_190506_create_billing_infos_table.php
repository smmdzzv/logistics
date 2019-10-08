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
            $table->char('stored_item_info_id', 26);
//            $table->integer('placesCount');
            $table->decimal('totalCubage');
            $table->decimal('totalWeight');
            $table->decimal('weightPerCube');
            $table->decimal('pricePerItem');
            $table->decimal('totalPrice', 10, 2);
            $table->decimal('discountPerCube', 10, 2);
            $table->decimal('totalDiscount', 10, 2);
            $table->decimal('count');
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

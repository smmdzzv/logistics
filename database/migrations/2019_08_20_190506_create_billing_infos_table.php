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
            $table->uuid('id')->primary();;
            $table->uuid('tariff_price_history_id')->index();
            $table->uuid('stored_item_info_id')->index();
//            $table->uuid('deleted_by_id', 26)->index()->nullable();
//            $table->integer('placesCount');
            $table->decimal('totalCubage', 10,3);
            $table->decimal('totalWeight', 10,3);
            $table->decimal('weightPerCube',10,3);
            $table->decimal('pricePerItem',10,2);
            $table->decimal('totalPrice', 10, 2);
            $table->decimal('discountPerCube', 10, 2);
            $table->decimal('totalDiscount', 10, 2);
            $table->decimal('count');
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
        Schema::dropIfExists('billing_infos');
    }
}

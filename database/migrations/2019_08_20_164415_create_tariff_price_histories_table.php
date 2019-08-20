<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTariffPriceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariff_price_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tariff_id');
            $table->unsignedBigInteger('branch_id');
            $table->integer('lowerLimit');
            $table->integer('mediumLimit');
            $table->integer('upperLimit');
            $table->double('discountForLowerLimit');
            $table->double('discountForMediumLimit');
            $table->double('pricePerCube');
            $table->double('agreedPricePerKg');
            $table->double('pricePerExtraKg');
            $table->integer('maxWeightPerCube');
            $table->integer('maxWeight');
            $table->integer('maxCubage');
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
        Schema::dropIfExists('tariff_price_histories');
    }
}

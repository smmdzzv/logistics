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
            $table->double('lowerLimit');
            $table->double('mediumLimit');
            $table->double('upperLimit');
            $table->double('discountForLowerLimit');
            $table->double('discountForMediumLimit');
            $table->double('pricePerCube');
            $table->double('agreedPricePerKg');
            $table->double('pricePerExtraKg');
            $table->double('maxWeightPerCube');
            $table->double('maxWeight');
            $table->double('maxCubage');
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

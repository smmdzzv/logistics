<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

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
            $table->char('id', 26)->primary();;
            $table->char('tariff_id', 26)->index();
            $table->char('branch_id', 26);
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
            $table->double('totalMoney');
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
        Schema::dropIfExists('tariff_price_histories');
    }
}

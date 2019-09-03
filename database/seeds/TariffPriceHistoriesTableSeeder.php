<?php

use App\Branch;
use App\Tariff;
use App\TariffPriceHistory;
use Illuminate\Database\Seeder;

class TariffPriceHistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newPrice = new TariffPriceHistory();
        $newPrice->lowerLimit = 110;
        $newPrice->mediumLimit = 170;
        $newPrice->upperLimit = 200;
        $newPrice->discountForLowerLimit = 10;
        $newPrice->discountForMediumLimit = 5;
        $newPrice->pricePerCube = 175;
        $newPrice->agreedPricePerKg = 0.77;
        $newPrice->pricePerExtraKg = 0.35;
        $newPrice->maxWeightPerCube = 250;
        $newPrice->maxCubage = 108;
        $newPrice->maxWeight = 27000;
        $newPrice->branch_id = Branch::first()->id;
        $newPrice->tariff_id = Tariff::first()->id;
        $newPrice->save();
    }


}

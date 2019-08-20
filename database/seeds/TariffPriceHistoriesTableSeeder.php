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
        $newPrice->lowerLimit = 100;
        $newPrice->mediumLimit = 120;
        $newPrice->upperLimit = 160;
        $newPrice->discountForLowerLimit = 10;
        $newPrice->discountForMediumLimit = 5;
        $newPrice->pricePerCube = 165;
        $newPrice->agreedPricePerKg = 0.47;
        $newPrice->pricePerExtraKg = 0.7;
        $newPrice->maxWeightPerCube = 250;
        $newPrice->maxCubage = 180;
        $newPrice->maxWeight = 30000;
        $newPrice->branch_id = Branch::first()->id;
        $newPrice->tariff_id = Tariff::first()->id;
        $newPrice->save();
    }
}

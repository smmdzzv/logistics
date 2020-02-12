<?php /** @noinspection ALL */

use App\Models\Branch;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use Illuminate\Database\Seeder;

class TariffPriceHistoriesTableSeeder extends Seeder
{
    private $tariffs = array();

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branchId = Branch::first()->id;
        $this->tariffs = Tariff::all();

        $newPrice = new TariffPriceHistory();
        $newPrice->lowerLimit = 90;
        $newPrice->mediumLimit = 120;
        $newPrice->upperLimit = 200;
        $newPrice->discountForLowerLimit = 5;
        $newPrice->discountForMediumLimit = 0;
        $newPrice->pricePerCube = 165;
        $newPrice->agreedPricePerKg = 0.73;
        $newPrice->pricePerExtraKg = 0.33;
        $newPrice->maxWeightPerCube = 250;
        $newPrice->maxCubage = 108;
        $newPrice->maxWeight = 27000;
        $newPrice->branch_id = $branchId;
        $newPrice->tariff_id = $this->getId('Обувь-У');
        $newPrice->totalMoney = 19602;
        $newPrice->save();

        $tariff=$this->getId('Пресс-У');
        TariffPriceHistory::create([
            'lowerLimit'=>90,
            'mediumLimit'=>120,
            'upperLimit'=>200,
            'discountForLowerLimit'=>10,
            'discountForMediumLimit'=>5,
            'pricePerCube'=>170,
            'agreedPricePerKg'=>0.7,
            'pricePerExtraKg'=>0.33,
            'maxWeightPerCube'=>200,
            'maxCubage'=>110,
            'maxWeight'=>22000,
            'branch_id'=>$branchId,
            'tariff_id'=>$tariff,
            'totalMoney' => 18700
        ]);

        $tariff=$this->getId('Запчасти-У');
        TariffPriceHistory::create([
            'lowerLimit'=>90,
            'mediumLimit'=>120,
            'upperLimit'=>170,
            'discountForLowerLimit'=>10,
            'discountForMediumLimit'=>5,
            'pricePerCube'=>170,
            'agreedPricePerKg'=>0.79,
            'pricePerExtraKg'=>0.33,
            'maxWeightPerCube'=>250,
            'maxCubage'=>108,
            'maxWeight'=>27000,
            'branch_id'=>$branchId,
            'tariff_id'=>$tariff,
            'totalMoney' => 21211
        ]);

        $tariff=$this->getId('Договорной-У');
        TariffPriceHistory::create([
            'lowerLimit'=>110,
            'mediumLimit'=>170,
            'upperLimit'=>200,
            'discountForLowerLimit'=>10,
            'discountForMediumLimit'=>5,
            'pricePerCube'=>170,
            'agreedPricePerKg'=>0.67,
            'pricePerExtraKg'=>0.33,
            'maxWeightPerCube'=>250,
            'maxCubage'=>108,
            'maxWeight'=>27000,
            'branch_id'=>$branchId,
            'tariff_id'=>$tariff,
            'totalMoney' => 21306.96
        ]);

//        $tariff=$this->getId('Кашкар');
//        TariffPriceHistory::create([
//            'lowerLimit'=>110,
//            'mediumLimit'=>170,
//            'upperLimit'=>200,
//            'discountForLowerLimit'=>10,
//            'discountForMediumLimit'=>5,
//            'pricePerCube'=>155,
//            'agreedPricePerKg'=>0.6,
//            'pricePerExtraKg'=>0.33,
//            'maxWeightPerCube'=>250,
//            'maxCubage'=>108,
//            'maxWeight'=>27000,
//            'branch_id'=>$branchId,
//            'tariff_id'=>$tariff,
//            'totalMoney' => 21306.96
//        ]);
    }

    private function getId($tariffName){
      foreach ($this->tariffs as $tariff){
          if($tariff->name === $tariffName)
              return $tariff->id;
      }
       return null;
    }
}

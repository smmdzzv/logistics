<?php

use App\Car;
use App\FuelConsumption;
use Illuminate\Database\Seeder;

class FuelConsumptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carId = Car::first()->id;

        $toChina = new FuelConsumption();
        $toChina->empty = 0.4;
        $toChina->loaded = 0.42;
        $toChina->hasTrailer = false;
        $toChina->destination = 'Китай';
        $toChina->car_id = $carId;
        $toChina->save();

        $toChinaTrailer = new FuelConsumption();
        $toChinaTrailer->empty = 0.401;
        $toChinaTrailer->loaded = 0.421;
        $toChinaTrailer->hasTrailer = true;
        $toChinaTrailer->destination = 'Китай';
        $toChinaTrailer->car_id = $carId;
        $toChinaTrailer->save();

        $fromChina = new FuelConsumption();
        $fromChina->empty = 0.39;
        $fromChina->loaded = 0.40;
        $fromChina->hasTrailer = false;
        $fromChina->destination = 'Таджикистан';
        $fromChina->car_id = $carId;
        $fromChina->save();

        $fromChinaTrailer = new FuelConsumption();
        $fromChinaTrailer->empty = 0.391;
        $fromChinaTrailer->loaded = 0.411;
        $fromChinaTrailer->hasTrailer = true;
        $fromChinaTrailer->destination = 'Таджикистан';
        $fromChinaTrailer->car_id = $carId;
        $fromChinaTrailer->save();


    }
}

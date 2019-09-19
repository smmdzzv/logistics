<?php

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $car = new Car();
        $car->number='TJ 44-98 СТ 01';
        $car->length = 8;
        $car->height = 2.6;
        $car->width = 3;
        $car->trailerNumber = '01 АА 20-66';
        $car->maxWeight = '40000';
        $car->maxCubage = '60';
        $car->save();
    }
}

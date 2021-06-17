<?php

use App\Models\Car;
use App\Models\CarProvider;
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
        $cars = [
            [
                "TJ44-98 CT 01",
                "1",
                "01 AA 2066",
                "1",
                "1",
                "1",
                "10000",
                "200",
                ""
            ],
            [
                "TJ 57-08 DD 01",
                "2",
                "01 AA 2065",
                "1",
                "1",
                "1",
                "10000",
                "200",
                ""
            ],
            [
                "TJ57-09 DD 01",
                "3",
                "01 AA 2058",
                "1",
                "2",
                "3",
                "50000",
                "120",
                ""
            ],
            [
                "TJ57-11 DD 01",
                "4",
                "01 AA 2036",
                "4",
                "5",
                "6",
                "7000",
                "140",
                ""
            ],
            [
                "TJ57-12 DD 01",
                "5",
                "01 AA 2064",
                "6",
                "7",
                "8",
                "9000",
                "300",
                ""
            ],
            [
                "TJ57-13 DD 01",
                "6",
                "01 AA 2061",
                "8",
                "7",
                "5",
                "4000",
                "250",
                ""
            ],
            [
                "TJ57-14 DD 01",
                "7",
                "01 AA 2059",
                "45",
                "40",
                "23",
                "11000",
                "500",
                ""
            ],
            [
                "TJ57-15 DD 01",
                "8",
                "01 AA 2062",
                "65",
                "65",
                "25",
                "12000",
                "600",
                ""
            ],
            [
                "TJ57-16 DD 01",
                "9",
                "01 AA 2060",
                "55",
                "45",
                "50",
                "13000",
                "700",
                ""
            ],
            [
                "TJ57-18 DD 01",
                "10",
                "01 AA 2067",
                "65",
                "65",
                "85",
                "14000",
                "500",
                ""
            ],
            [
                "TJ61-54 EE 01",
                "11",
                "01 AA 2088",
                "50",
                "40",
                "60",
                "15000",
                "900",
                ""
            ],
            [
                "TJ69-78 DD 01",
                "12",
                "01 AA 2091",
                "23",
                "33",
                "43",
                "16000",
                "600",
                ""
            ],
            [
                "TJ69-82 DD 01",
                "13",
                "01 A 2092",
                "66",
                "56",
                "46",
                "17000",
                "700",
                ""
            ],
            [
                "TJ69-84 DD 01",
                "14",
                "01 AA 2095",
                "66",
                "64",
                "67",
                "12000",
                "600",
                ""
            ],
            [
                "TJ70-85 DD 01",
                "15",
                "01 AA 2090",
                "55",
                "45",
                "65",
                "14000",
                "500",
                ""
            ],
            [
                "TJ69-81 DD 01",
                "16",
                "01 AA 2087",
                "63",
                "64",
                "55",
                "10000",
                "600",
                ""
            ]
        ];

        $carProvider = CarProvider::create([
            'name' => 'Дуоб'
        ]);

        foreach ($cars as $carData) {
            $car = new Car();
            $car->car_provider_id = $carProvider->id;
            $car->number = $carData[0];
            $car->trailerNumber = $carData[2];
            $car->length =  $carData[3];
            $car->width = $carData[4];
            $car->height = $carData[5];
            $car->maxWeight = $carData[6];
            $car->maxCubage = $carData[7];
            $car->trailerMaxWeight = '8000';
            $car->trailerMaxCubage = '50';
            $car->fuelAmount = 0;
            $car->save();

            $car->storeDefaultConsumptions();
        }
    }
}

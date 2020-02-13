<?php

use App\Models\Branch;
use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tariffs = [
            'Обувь-У' => ['Барои борхои Обувь', Branch::where('name', 'Урумчи')->first()->id],
            'Пресс-У' => ['Барои борхои ПРЕСС ва ХАЛТА (борхои птичкадорхо)', Branch::where('name', 'Урумчи')->first()->id],
            'Запчасти-У' => ['Барои борхои Запчасть, Хозтовар, канстовар', Branch::where('name', 'Урумчи')->first()->id],
            'Договорной-У' => ['Барои борхои Договоркардашуда', Branch::where('name', 'Урумчи')->first()->id],
            'Дог. запчасти-У' => ['Барои борхои хархелаи Урумчи', Branch::where('name', 'Урумчи')->first()->id],


            'Обувь-И' => ['Барои борхои Обувь', Branch::where('name', 'ИВУ')->first()->id],
            'Пресс-И' => ['Барои борхои ПРЕСС ва ХАЛТА (борхои птичкадорхо)', Branch::where('name', 'ИВУ')->first()->id],
            'Запчасти-И' => ['Барои борхои Запчасть, Хозтовар, канстовар', Branch::where('name', 'ИВУ')->first()->id],
            'Дог. запчасти-И' => ['Барои борхои хархелаи ИВУ', Branch::where('name', 'ИВУ')->first()->id],
            'Догоорной-И' => ['Барои борхои Пресс, халта, птичкадорхо, договорной аз 350 кг боло', Branch::where('name', 'ИВУ')->first()->id],

            'Кашкар' => ['Борхои аз Кашкар кабулкардашуда', Branch::where('name', 'Кашкар')->first()->id],
            'Обувь-К' =>['Барои борхои хархелаи Кашкар', Branch::where('name', 'Кашкар')->first()->id],
            'Пресс-К' => ['Барои борхо пресс, халта, птичкадорхо', Branch::where('name', 'Кашкар')->first()->id],
            'Запчасти-К' => ['Барои борхои Запчасть', Branch::where('name', 'Кашкар')->first()->id],
            'Договрной-К' => ['Барои борхои Пресс, халта, птичкадорхо, договорной аз 350 кг боло', Branch::where('name', 'Кашкар')->first()->id],
            'Дог. запчасти-К' => ['Барои борхои хархелаи Кашкар', Branch::where('name', 'Кашкар')->first()->id],

            'Обувь-Г' =>  ['Барои борхои обувь. Кроссовки из Гуанджоу', Branch::where('name', 'Гуанджоу')->first()->id],
            'Пресс-Г' =>  ['Барои борхои пресс птичка. Халтадорхо', Branch::where('name', 'Гуанджоу')->first()->id],
            'Запчасти-Г' =>  ['Барои борхои Запчасть', Branch::where('name', 'Гуанджоу')->first()->id],
            'Дог. запчасти-Г' =>  ['Барои борхои хархелаи Гуанджоу', Branch::where('name', 'Гуанджоу')->first()->id],
            'Договорной-Г' =>  ['Барои борхои Пресс, халта, птичкадорхо, договорной аз 350 кг боло', Branch::where('name', 'Гуанджоу')->first()->id]
        ];

        foreach ($tariffs as $key => $value) {
            $tariff = new Tariff();
            $tariff->name = $key;
            $tariff->description = $value[0];
            $tariff->branch_id = $value[1];
            $tariff->save();
        }
    }
}

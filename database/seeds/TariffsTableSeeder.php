<?php

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
            'Обувь-У' => 'Барои борхои Обувь',
            'Пресс-У' => 'Барои борхои ПРЕСС ва ХАЛТА (борхои птичкадорхо)',
            'Запчасти-У' => 'Барои борхои Запчасть, Хозтовар, канстовар',
            'Договорной-У' => 'Барои борхои Договоркардашуда',
            'Кашкар' => 'Борхои аз кашкар кабулкардашуда',
            'Обувь-И' => 'Барои борхои Обувь',
            'Пресс-И' => 'Барои борхои ПРЕСС ва ХАЛТА (борхои птичкадорхо)',
            'Запчасти-И' => 'Барои борхои Запчасть, Хозтовар, канстовар',
            'Договорной' =>  'Барои борхои Договоркардашуда'];

        foreach ($tariffs as $key=>$value){
            $tariff = new Tariff();
            $tariff->name = $key;
            $tariff->description = $value;
            $tariff->save();
        }
    }
}

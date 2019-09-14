<?php

use App\Item;
use App\Tariff;
use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            'Мешок' => 'Обувь-У',
            'Пресс' => 'Обувь-У',
            'Коробка' => 'Обувь-У',
            'Дизтопливо' => 'Пресс-У',
            'Амортизатор большой' => 'Запчасти-У'
        ];

        foreach ($items as $key=>$value){
            $item = new Item();
            $item->name = $key;
            $item->isPriceCustom = false;
            $item->tariff_id = Tariff::where('name',$value)->first()->id;
            $item->unit = 'шт';
            $item->save();
        }
    }
}

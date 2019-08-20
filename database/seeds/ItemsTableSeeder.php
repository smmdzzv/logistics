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
            'Мешок' => 1,
            'Пресс' => 1,
            'Коробка' => 1,
            'Дизтопливо' => 2,
            'Амортизатор большой' => 3,
        ];

        foreach ($items as $key=>$value){
            $item = new Item();
            $item->name = $key;
            $item->isPriceCustom = false;
            $item->tariff_id = Tariff::find($value)->id;
            $item->unit = 'шт';
            $item->save();
        }
    }
}

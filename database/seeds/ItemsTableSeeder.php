<?php

use App\Models\StoredItems\Item;
use App\Models\Tariff;
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
            $item->onlyCustomPrice = false;
            $item->applyDiscount = true;
            $item->tariffId = Tariff::where('name',$value)->first()->id;
            $item->unit = 'шт';
            $item->save();
        }
    }
}

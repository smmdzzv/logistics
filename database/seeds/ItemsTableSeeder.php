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
        $items = [
        [
            "Мешок",
            0,
            0
        ],
        [
            "Пресс",
            1,
            0
        ],
        [
            "Коробка",
            0,
            0
        ],
        [
            "Жесть",
            0,
            1
        ],
        [
            "Железо",
            0,
            1
        ],
        [
            "Мебель",
            0,
            0
        ],
        [
            "Халта",
            1,
            0
        ],
        [
            "Пластик",
            0,
            1
        ],
        [
            "Нассосы",
            0,
            1
        ],
        [
            "Игрушки",
            0,
            0
        ],
        [
            "Рубашки",
            0,
            0
        ],
        [
            "Профиль",
            0,
            0
        ],
        [
            "Люстра",
            0,
            0
        ],
        [
            "Канцтовары",
            0,
            0
        ],
        [
            "Кафель",
            0,
            1
        ],
        [
            "Хозтовары",
            0,
            0
        ],
        [
            "Запчасти",
            0,
            0
        ],
        [
            "Упаковка",
            0,
            0
        ],
        [
            "Дверь",
            0,
            0
        ],
        [
            "Глушитель",
            0,
            0
        ],
        [
            "Рисоры",
            0,
            1
        ],
        [
            "Ящик",
            0,
            0
        ],
        [
            "Ламинат",
            0,
            0
        ],
        [
            "Станок",
            0,
            0
        ],
        [
            "Фанер",
            0,
            0
        ],
        [
            "Бампер",
            0,
            0
        ],
        [
            "Сим",
            0,
            1
        ],
        [
            "Зонтик",
            1,
            0
        ],
        [
            "Кухонный набор",
            0,
            0
        ],
        [
            "Рулон",
            0,
            0
        ],
        [
            "Винтель",
            0,
            0
        ]
    ];

        foreach ($items as $itemData){
            $item = new Item();
            $item->name = $itemData[0];
            $item->calculateByNormAndWeight = $itemData[1];
            $item->onlyCustomPrice = $itemData[2];
            $item->applyDiscount = true;
            $item->unit = 'шт';
            $item->save();
        }
    }
}

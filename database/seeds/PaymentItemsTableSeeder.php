<?php

use App\Models\Till\PaymentItem;
use Illuminate\Database\Seeder;

class PaymentItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentItem::create(
            [
                'title' => 'Оплата заказа',
                'description' => 'Плата за принятый заказ',
                'type' => 'in'
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Пополнение баланса',
                'description' => 'Пополнение долларового счета пользователя',
                'type' => 'in'
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Аванс',
                'description' => 'Выплата аванса с заработной платы',
                'type' => 'out'
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Зарплата',
                'description' => 'Выплата заработной платы',
                'type' => 'out'
            ]
        );
    }
}

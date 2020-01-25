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
                'title' => 'Списание с баланса',
                'description' => 'Списание денег с баланса клиента в счет оплаты заказы',
                'type' => 'internal'
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Оплата заказа',
                'description' => 'Оплата заказа наличными без пополнения баланса пользователя',
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

        PaymentItem::create(
            [
                'title' => 'Прием наличных',
                'description' => 'Прием наличных на счет Дуоб',
                'type' => 'in'
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Выдача наличных',
                'description' => 'Выдача наличных со счета Дуоб',
                'type' => 'out'
            ]
        );
    }
}

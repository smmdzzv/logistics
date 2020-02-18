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
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Пополнение баланса',
                'description' => 'Пополнение долларового счета пользователя',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Аванс',
                'description' => 'Выплата аванса с заработной платы',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Зарплата',
                'description' => 'Выплата заработной платы',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Прием наличных',
                'description' => 'Прием наличных на счет Дуоб',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Выдача наличных',
                'description' => 'Выдача наличных со счета Дуоб',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Перевод между филиалами',
                'description' => 'Перевод денег между филиалами',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Перевод между счетами',
                'description' => 'Перевод денег между счетами одного филиала',
            ]
        );
    }
}

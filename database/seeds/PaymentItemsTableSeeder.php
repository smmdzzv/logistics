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
                'title' => 'Прием наличных средств',
                'description' => 'Прием наличных срдеств на счет филиала Дуоб',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Выдача наличных средств',
                'description' => 'Выдача наличных со счета филиала Дуоб',
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
                'title' => 'Перевод между счетами филиала',
                'description' => 'Перевод денег между счетами одного филиала',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Обмен валют',
                'description' => 'Обмен валют для клиентов',
            ]
        );

        PaymentItem::create(
            [
                'title' => 'Бонус',
                'description' => 'Бонус для клиентов',
            ]
        );
    }
}

<?php

use App\Models\Till\Expenditure;
use Illuminate\Database\Seeder;

class ExpendituresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Expenditure::create(
            [
                'title' => 'Оплата заказа',
                'description' => 'Плата за принятый заказ',
                'type' => 'in'
            ]
        );

        Expenditure::create(
            [
                'title' => 'Пополнение баланса',
                'description' => 'Пополнение долларового счета пользователя',
                'type' => 'in'
            ]
        );

        Expenditure::create(
            [
                'title' => 'Аванс',
                'description' => 'Выплата аванса с заработной платы',
                'type' => 'out'
            ]
        );

        Expenditure::create(
            [
                'title' => 'Зарплата',
                'description' => 'Выплата заработной платы',
                'type' => 'out'
            ]
        );
    }
}

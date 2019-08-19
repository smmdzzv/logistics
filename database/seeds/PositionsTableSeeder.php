<?php

use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Position::create([
           'name'=>'Администратор',
           'description' => 'Обязанности системного администратора: 1. Иногда что-то делать'
        ]);
    }
}

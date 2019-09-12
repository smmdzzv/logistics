<?php

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $china = new Country();
        $china->name = 'Китай';
        $china->save();

        $tjk = new Country();
        $tjk->name = 'Таджикистан';
        $tjk->save();
    }
}

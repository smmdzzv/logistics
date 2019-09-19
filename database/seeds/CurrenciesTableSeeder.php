<?php

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            'доллар' => 'ДОЛ',
            'сомони' => 'СОМ',
            'рубль' => 'РУБ'
            ];

        foreach ($currencies as $key=>$value){
            $currency = new Currency();
            $currency->name = $key;
            $currency->shortName = $value;
            $currency->save();
        }
    }
}

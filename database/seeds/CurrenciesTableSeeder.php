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
            'доллар' => ['ДОЛ', 'USD'],
            'сомони' => ['СОМ', 'TJS'],
            'рубль' => ['РУБ', 'RUB'],
            'юань' => ['ЮАН', 'CHY']
            ];

        foreach ($currencies as $key=>$value){
            $currency = new Currency();
            $currency->name = $key;
            $currency->shortName = $value[0];
            $currency->isoName = $value[1];
            $currency->save();
        }
    }
}

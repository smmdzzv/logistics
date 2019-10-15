<?php

use App\Models\Country;
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
            'доллар' => ['ДОЛ', 'USD', 'США'],
            'сомони' => ['СОМ', 'TJS', 'Таджикистан'],
            'рубль' => ['РУБ', 'RUB', 'Россия'],
            'юань' => ['ЮАН', 'CHY', 'Китай']
            ];

        foreach ($currencies as $key=>$value){
            $country = Country::where('name', $value[2])->first();

            $currency = new Currency();
            $currency->name = $key;
            $currency->shortName = $value[0];
            $currency->isoName = $value[1];
            $currency->country_id = $country->id;
            $currency->save();
        }
    }
}

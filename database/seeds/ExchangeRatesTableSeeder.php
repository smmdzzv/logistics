<?php

use App\Models\Currency;
use App\Models\Till\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dollar = Currency::where('isoName', 'USD')->first();
        $somoni = Currency::where('isoName', 'TJS')->first();

        $exchange = new ExchangeRate();
        $exchange->from_currency_id = $dollar->id;
        $exchange->to_currency_id = $somoni->id;
        $exchange->coefficient = 9.69;
        $exchange->save();

        $exchange = new ExchangeRate();
        $exchange->from_currency_id = $somoni->id;
        $exchange->to_currency_id = $dollar->id;
        $exchange->coefficient = 0.1;
        $exchange->save();
    }
}

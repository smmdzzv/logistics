<?php

use App\Models\Currency;
use App\Models\Till\ExchangeRate;
use Illuminate\Database\Seeder;

class MoneyExchangesTableSeeder extends Seeder
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
        $exchange->fromCurrency = $dollar->id;
        $exchange->toCurrency = $somoni->id;
        $exchange->coefficient = 9.69;
        $exchange->save();

        $exchange = new ExchangeRate();
        $exchange->fromCurrency = $somoni->id;
        $exchange->toCurrency = $dollar->id;
        $exchange->coefficient = 0.1;
        $exchange->save();
    }
}

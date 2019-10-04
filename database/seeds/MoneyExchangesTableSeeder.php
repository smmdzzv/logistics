<?php

use App\Models\Currency;
use App\Models\Till\MoneyExchange;
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

        $exchange = new MoneyExchange();
        $exchange->from = $dollar->id;
        $exchange->to = $somoni->id;
        $exchange->coefficient = 9.69;
        $exchange->save();

        $exchange = new MoneyExchange();
        $exchange->from = $somoni->id;
        $exchange->to = $dollar->id;
        $exchange->coefficient = 0.1;
        $exchange->save();
    }
}

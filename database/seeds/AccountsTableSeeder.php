<?php

use App\Account;
use App\Currency;
use App\User;
use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = new Account();
        $account->balance = 0;
        $account->currency_id = Currency::first()->id;
        $account->owner_id= User::first()->id;
        $account->save();
    }
}

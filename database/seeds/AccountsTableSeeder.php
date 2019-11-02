<?php

use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\Account;
use App\Models\Currency;
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
        $owner = LegalEntity::first();
        $usdId = Currency::where('isoName', 'USD')->first()->id;

        $currencies = Currency::all();

        $accounts = $currencies->map(function ($currency){
            return new Account([
                'balance' => 0,
                'description' => "Cчет Дуоб {$currency->isoName} ({$currency->name})",
                'currencyId' => $currency->id
            ]);
        });

        $owner->accounts()->saveMany($accounts);


        $users = User::all();

        foreach ($users as $user){
            $account = new Account();
            $account->currencyId = $usdId;
            $account->balance = 0;
            $account->description = 'Долларовый счет пользователя '.$user->name;

            $user->accounts()->save($account);
        }
    }
}

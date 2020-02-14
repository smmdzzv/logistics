<?php

use App\Models\Branch;
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
                'currency_id' => $currency->id
            ]);
        });

        $owner->accounts()->saveMany($accounts);

        $branches = Branch::all();

        foreach ($branches as $branch){
            $accounts = [];
            foreach ($currencies as $currency){
                $accounts[] = new Account([
                    'balance' => 0,
                    'description' => "Cчет {$branch->name} {$currency->isoName} ({$currency->name})",
                    'currency_id' => $currency->id
                ]);
            }

            $branch->accounts()->saveMany($accounts);
        }


        $users = User::all();

        foreach ($users as $user){
            $account = new Account();
            $account->currency_id = $usdId;
            $account->balance = 0;
            $account->description = 'Долларовый счет пользователя '.$user->name;

            $user->accounts()->save($account);
        }
    }
}

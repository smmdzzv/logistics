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
        $currencyId = Currency::where('isoName', 'USD')->first()->id;

        $owner->accounts()->create([
            'balance' => 0,
            'description' => "Основной счет Дуоб",
            'currencyId' => $currencyId
        ]);

        $owner = User::where('code', '1345')->first();

        $owner->accounts()->create([
            'balance' => 100,
            'description' => "Долларовый счет Бахтиерова",
            'currencyId' => $currencyId
        ]);
    }
}

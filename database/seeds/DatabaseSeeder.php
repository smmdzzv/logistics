<?php

use App\Branch;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(CountriesTableSeeder::class);
         $this->call(BranchesTableSeeder::class);
         $this->call(TariffsTableSeeder::class);
         $this->call(ItemsTableSeeder::class);
         $this->call(CurrenciesTableSeeder::class);
         $this->call(RolesTableSeeder::class);
         $this->call(PositionsTableSeeder::class);
         $this->call(UsersTableSeeder::class);
         $this->call(LegalEntitesTableSeeder::class);
         $this->call(AccountsTableSeeder::class);

         $this->call(StoredItemsTableSeeder::class);

         $this->call(CarsTableSeeder::class);
         $this->call(FuelConsumptionsTableSeeder::class);
         $this->call(TariffPriceHistoriesTableSeeder::class);

         $this->call(PaymentItemsTableSeeder::class);
         $this->call(MoneyExchangesTableSeeder::class);
    }
}

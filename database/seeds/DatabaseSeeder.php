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
         $this->call(BranchesTableSeeder::class);
         $this->call(TariffsTableSeeder::class);
         $this->call(ItemsTableSeeder::class);
         $this->call(CurrenciesTableSeeder::class);
         $this->call(RolesTableSeeder::class);
         $this->call(PositionsTableSeeder::class);
         $this->call(UsersTableSeeder::class);
         $this->call(AccountsTableSeeder::class);
         $this->call(StoredItemsTableSeeder::class);
    }
}

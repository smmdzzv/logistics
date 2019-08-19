<?php

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
         $this->call(TariffsTableSeeder::class);
         $this->call(RolesTableSeeder::class);
         $this->call(PositionsTableSeeder::class);
         $this->call(UsersTableSeeder::class);
    }
}

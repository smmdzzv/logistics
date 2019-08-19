<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleEmployee = new Role();
        $roleEmployee->name = 'employee';
        $roleEmployee->description = 'Системная роль - сотрудник';
        $roleEmployee->save();

        $roleClient = new Role();
        $roleClient->name = 'client';
        $roleClient->description = 'Системная роль - клиент';
        $roleClient->save();

        $roleAdmin = new Role();
        $roleAdmin->name = 'admin';
        $roleAdmin->description = 'Системная роль - администратор';
        $roleAdmin->save();
    }
}

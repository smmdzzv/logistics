<?php

use App\Models\Role;
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
        $roleClient = new Role();
        $roleClient->name = 'client';
        $roleClient->title = 'Клиент';
        $roleClient->description = 'Системная роль - клиент';
        $roleClient->save();

        $roleEmployee = new Role();
        $roleEmployee->name = 'driver';
        $roleEmployee->title = 'Водитель';
        $roleEmployee->description = 'Системная роль - водитель';
        $roleEmployee->save();

        $roleEmployee = new Role();
        $roleEmployee->name = 'employee';
        $roleEmployee->title = 'Сотрудник';
        $roleEmployee->description = 'Системная роль - сотрудник';
        $roleEmployee->save();

        $roleEmployee = new Role();
        $roleEmployee->name = 'manager';
        $roleEmployee->title = 'Менеджер';
        $roleEmployee->description = 'Системная роль - менеджер';
        $roleEmployee->save();

        $roleAdmin = new Role();
        $roleAdmin->name = 'admin';
        $roleAdmin->title = 'Администратор';
        $roleAdmin->description = 'Системная роль - администратор';
        $roleAdmin->save();
    }
}

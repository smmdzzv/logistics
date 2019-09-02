<?php

use App\User;
use App\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::where('name', 'admin')->first();

        $positionId = \App\Position::where('name', 'Администратор')->first()->id;

        $branchId = \App\Branch::first()->id;

        $user = User::create([
            'name'=>'Султоназар Мамадазизов',
            'position_id' => $positionId,
            'branch_id' => $branchId,
            'password' => Hash::make('asdf1234'),
            'email' => 'test@test.com',
            'code'=>'12345'
        ]);

        $user->roles()->attach($roleAdmin);

        $roleClient = Role::where('name', 'client')->first();


        $client = User::create([
            'name'=>'Рахматшох Бахтиеров',
            'branch_id' => $branchId,
            'password' => Hash::make('asdf1234'),
            'email' => 'test2@test.com',
            'code'=>'1345'
        ]);

        $client->roles()->attach($roleClient);
    }
}

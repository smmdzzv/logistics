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

        $user = User::create([
            'name'=>'Султоназар Мамадазизов',
            'position_id' => $positionId,
            'password' => Hash::make('asdf1234'),
            'email' => 'test@test.com'
        ]);

        $user->roles()->attach($roleAdmin);
    }
}

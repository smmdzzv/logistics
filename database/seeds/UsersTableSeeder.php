<?php

use App\Models\Branch;
use App\Models\Position;
use App\User;
use App\Models\Role;
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
        $roleClient = Role::where('name', 'client')->first();
        $roleEmployee = Role::where('name', 'employee')->first();
        $roleDriver = Role::where('name', 'driver')->first();
        $roleCashier = Role::where('name', 'cashier')->first();

        $positionId = Position::where('name', 'Администратор')->first()->id;

        $branchId = Branch::first()->id;

        $user = User::create([
            'name'=>'Султоназар Мамадазизов',
            'position_id' => $positionId,
            'branch_id' => $branchId,
            'password' => Hash::make('asdf1234'),
            'email' => 'test@test.com',
            'code'=>'12345'
        ]);

        $user->roles()->attach($roleAdmin);
        $user->roles()->attach($roleEmployee);

        $client = User::create([
            'name'=>'Рахматшох Бахтиеров',
            'branch_id' => $branchId,
            'password' => Hash::make('asdf1234'),
            'email' => 'test2@test.com',
            'code'=>'1345'
        ]);

        $client->roles()->attach($roleClient);

        $user = User::create([
            'name'=>'Давлатмуродов Мурод',
            'branch_id' => $branchId,
            'phone' => '53183421',
            'password' => Hash::make('asdf1234'),
            'email' => 'test3@test.com',
            'code'=>'1332'
        ]);

        $user->roles()->attach($roleEmployee);

        $user = User::create([
            'name'=>'Ширинбеков Ширин',
            'branch_id' => $branchId,
            'phone' => '25138483',
            'password' => Hash::make('asdf1234'),
            'email' => 'test4@test.com',
            'code'=>'2552'
        ]);

        $user->roles()->attach($roleClient);

        $user = User::create([
            'name'=>'Саломатшоев Некруз',
            'branch_id' => $branchId,
            'phone' => '5138432',
            'password' => Hash::make('asdf1234'),
            'email' => 'test5@test.com',
            'code'=>'3255'
        ]);

        $user->roles()->attach($roleClient);

        $user = User::create([
            'name'=>'Инноятов Максуд',
            'branch_id' => $branchId,
            'phone' => '12312414',
            'password' => Hash::make('asdf1234'),
            'email' => 'test51@test.com',
            'code'=>'13255'
        ]);

        $user->roles()->attach($roleDriver);

        $user = User::create([
            'name'=>'Йодалиев Махсудшох',
            'branch_id' => $branchId,
            'phone' => '12312414',
            'password' => Hash::make('asdf1234'),
            'email' => 'test52@test.com',
            'code'=>'132215'
        ]);

        $user->roles()->attach($roleCashier);

        $user = User::create([
            'name'=>'Джалолов Шамс',
            'branch_id' => $branchId,
            'phone' => '+92656565656565',
            'password' => Hash::make('asdf1234'),
            'email' => 'duob@test.com',
            'code'=>'111111'
        ]);

        $user->roles()->attach($roleAdmin);
    }
}

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
//        $roleClient = Role::where('name', 'client')->first();
//        $roleEmployee = Role::where('name', 'employee')->first();
        $roleDriver = Role::where('name', 'driver')->first();
//        $roleCashier = Role::where('name', 'cashier')->first();

        $positionId = Position::where('name', 'Администратор')->first()->id;

        $branchId = Branch::first()->id;

        $user = User::create([
            'name'=>'Султоназар Мамадазизов',
            'position_id' => $positionId,
            'branch_id' => $branchId,
            'password' => Hash::make('Dhtvtyysq92'),
            'email' => 'sultonazar.mamadazizov@mail.ru',
            'code'=>'1010011'
        ]);

        $user->roles()->attach($roleAdmin);

        $admin2 = User::create([
            'name'=>'Рахматшох Бахтиеров',
            'branch_id' => $branchId,
            'password' => Hash::make('WsegeTsde'),
            'email' => 'ramesh14@mail.ru',
            'code'=>'1010010'
        ]);

        $admin2->roles()->attach($roleAdmin);

        $user = User::create([
            'name'=>'Джалолов Шамс',
            'branch_id' => $branchId,
            'phone' => '',
            'password' => Hash::make('15sdTit'),
            'email' => 'info@duob.tj',
            'code'=>'111111'
        ]);

        $user->roles()->attach($roleAdmin);


        $user = User::create([
            'name'=>'Худжамова Мукаррама',
            'branch_id' => $branchId,
            'phone' => '557001025',
            'password' => Hash::make('reToier23'),
            'email' => 'miranshabozov@gmail.com',
            'code'=>'0506'
        ]);

        $user->roles()->attach($roleAdmin);

        $driversData = [
        [
            "Рахимов Мухаммаджон Бобиевич",
            "1111",
            "rahimov@mail.ru",
            "555558935"
        ],
        [
            "Назаров Давлатджон Зарифович",
            "2222",
            "nzarov@mail.ru",
            "555550572"
        ],
        [
            "Сурхов Толиб Тоирович",
            "3333",
            "tolib@mail.ru",
            "555556734"
        ],
        [
            "Имомов Курбоназар Урокович",
            "4444",
            "kurbonazar@mail.ru",
            "555556441"
        ],
        [
            "Шарифов Абдуджаббор Абдуразокович",
            "5555",
            "abdujabbor@mail.ru",
            "555550571"
        ],
        [
            "Мусофиров Давлтшо Юсупович",
            "6666",
            "davlatsho@mail.ru",
            "555556735"
        ],
        [
            "Холматов Абдужалил Махкамович",
            "7777",
            "abdujalil@mail.ru",
            "555556735"
        ],
        [
            "Саидов Косимджон Нуралиевич",
            "8888",
            "kosimjon@mail.ru",
            "934027791"
        ],
        [
            "Гуломалиев Назри Абдулалишоевич",
            "9999",
            "nazri@mail.ru",
            "934652065"
        ],
        [
            "Зокиров Риводжиддин Кодирович",
            "0000",
            "rivojiddin@mail.u",
            "918129498"
        ],
        [
            "Сабзалиев Далер Илолович",
            "1212",
            "daler@mail.ru",
            "502115404"
        ],
        [
            "Толибхонов Мародчон Хоркашович",
            "1313",
            "maroodxon@mail.ru",
            "501062329"
        ],
        [
            "Давлатбеков Муслим Лолоевич",
            "1414",
            "muslim@mail.ru",
            "931284482"
        ],
        [
            "Мазамбеков Фазлиддин Махрамбекович",
            "1515",
            "fazliddin@mail.ru",
            "501003105"
        ],
        [
            "Каримов Алишер Турсунмуродович",
            "1616",
            "alisher@mail.ru",
            "915857544"
        ],
        [
            "Алмарсов Хурсанбек Абосович",
            "1717",
            "hursanbek@mail.ru",
            "901019599"
        ]
    ];

        foreach ($driversData as $data){
            $user = User::create([
                'name'=> $data[0],
                'branch_id' => $branchId,
                'phone' => $data[3],
                'password' => Hash::make('asdf1234'),
                'email' => $data[2],
                'code'=> $data[1]
            ]);

            $user->roles()->attach($roleDriver);
        }
    }
}

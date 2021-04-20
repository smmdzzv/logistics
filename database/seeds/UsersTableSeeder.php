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
            'name' => 'Султоназар Мамадазизов',
            'position_id' => $positionId,
            'branch_id' => $branchId,
            'password' => Hash::make('Dhtvtyysq92'),
            'email' => 'sultonazar.mamadazizov@mail.ru',
            'code' => '1010011'
        ]);

        $user->roles()->attach($roleAdmin);

        $admin2 = User::create([
            'name' => 'Рахматшох Бахтиеров',
            'branch_id' => $branchId,
            'password' => Hash::make('WsegeTsde'),
            'email' => 'ramesh14@mail.ru',
            'code' => '1010010'
        ]);

        $admin2->roles()->attach($roleAdmin);

        $user = User::create([
            'name' => 'Джалолов Шамсудин',
            'branch_id' => $branchId,
            'phone' => '',
            'password' => Hash::make('15sdTit'),
            'email' => 'info@duob.tj',
            'code' => '11111'
        ]);

        $user->roles()->attach($roleAdmin);


        $user = User::create([
            'name' => 'Далер',
            'branch_id' => $branchId,
            'phone' => '557001025',
            'password' => Hash::make('1234567890'),
            'email' => 'daler@gmail.com',
            'code' => '222222'
        ]);

        $user->roles()->attach($roleAdmin);

        $user = User::create([
            'name' => 'Курбонов Сорбон',
            'branch_id' => $branchId,
            'phone' => '557001025',
            'password' => Hash::make('123456'),
            'email' => 'test72@test.com',
            'code' => '0172'
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

        foreach ($driversData as $data) {
            $user = User::create([
                'name' => $data[0],
                'branch_id' => $branchId,
                'phone' => $data[3],
                'password' => Hash::make('asdf1234'),
                'email' => $data[2],
                'code' => $data[1]
            ]);

            $user->roles()->attach($roleDriver);
        }

        $employees = [
            [
                "Абдулхаев Амурулло",
                "Душанбе",
                "amrullo@mail.ru",
                "cashier",
                'hrhdrt@3fe',
                '12121'
            ],
            [
                "Нурмахмадов Азам",
                "Душанбе",
                "azam@mail.ru",
                "employee",
                'Ekiejet32',
                '14141'
            ],
            [
                "Ализода Рустам",
                "Душанбе",
                "rustam@mail.ru",
                "employee",
                'affefe',
                '15151'
            ],
            [
                "Джалолов Шерафган",
                "Урумчи",
                "sherafgan@mail.ru",
                "storekeeper",
                'sfef@3fe',
                '17171'
            ],
            [
                "Мавлонов Хочаназар",
                "Душанбе",
                "hojanazar@mail.ru",
                "employee",
                'sfeaWdsfefW',
                '88881'
            ],
            [
                "Маликова Фируза",
                "Душанбе",
                "malikova@mail.ru",
                "cashier",
                'Efse@3dfse',
                '99991'
            ],
            [
                "Муродов Шерали",
                "Кашкар",
                "sherali@mail.ru",
                "storekeeper",
                'Egsehe3e',
                '10101'
            ],
            [
                "Назаров Назарали",
                "Душанбе",
                "nazarali@mail.ru",
                "employee",
                'fsef#sef3',
                '44441'
            ],
            [
                "Одинаев Рустам",
                "ИВУ",
                "rustam2@mail.ru",
                "manager",
                'Efse',
                '55551'
            ],
            [
                "Раджабов Дилшод",
                "Душанбе",
                "dilshod@mail.ru",
                "employee",
                'Fesef332df',
                '66661'
            ],
            [
                "Раджабов Иброхим",
                "Душанбе",
                "ibrohm@mail.ru",
                "employee",
                'sfef3yj2',
                '78781'
            ],
            [
                "Сатторов Чамшед",
                "Душанбе",
                "jamshed@mail.ru",
                "storekeeper",
                'fafese23',
                '45451'
            ],
            [
                "Сафаров Рахматулло",
                "Душанбе",
                "rahmatullo@mail.ru",
                "employee",
                'jejnei35sd',
                '96961'
            ],
            [
                "Эргашев Бахтиёр",
                "Кашкар",
                "baxtiyor74_74@mail.ru",
                "storekeeper",
                'tbeyeuj3',
                '54541'
            ],
            [
                "Эргашев Хайрулло",
                "Урумчи",
                "hayrullo@mail.ru",
                "storekeeper",
                'esbe3dsesf',
                '1011'
            ],
        ];

        foreach ($employees as $employee) {
            $user = User::create([
                'name' => $employee[0],
                'branch_id' => Branch::where('name', $employee[1])->first()->id,
                'email' => $employee[2],
                'password' => Hash::make($employee[4]),
                'code' => $employee[5]
            ]);

            $user->roles()->attach(Role::where('name', $employee[3])->first());
        }
    }
}

<?php

use App\Branch;
use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = [
            'Душанбе' => '',
            'Урумчи' => '',
            'ИВУ' => '',
            'Гуанджоу' => '',
            'Кашкар' => '',
        ];

        foreach ($branches as $name=>$description){
            $branch = new Branch();
            $branch->name = $name;
            $branch->save();
        }
    }
}

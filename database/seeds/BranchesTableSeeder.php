<?php

use App\Models\Branch;
use App\Models\Branches\Storage;
use App\Models\Country;
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
            'Душанбе' => 'Таджикистан',
            'Урумчи' => 'Китай',
            'ИВУ' => 'Китай',
            'Гуанджоу' => 'Китай',
            'Кашкар' => 'Китай',
        ];

        foreach ($branches as $name=>$country){
            $branch = new Branch();
            $branch->name = $name;
            $branch->country = Country::where('name', $country)->first()->id;
            $branch->save();

            $storage = new Storage([
                'name' => 'main'
            ]);
            $branch->storages()->save($storage);
        }
    }
}

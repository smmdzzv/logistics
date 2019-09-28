<?php

use App\Models\LegalEntities\LegalEntity;
use Illuminate\Database\Seeder;

class LegalEntitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entity = new LegalEntity();
        $entity->type = 'Общество с ограниченной отвественностью';
        $entity->name = 'Дуоб';
        $entity->save();
    }
}

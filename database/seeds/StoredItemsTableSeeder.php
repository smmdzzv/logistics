<?php

use App\Models\Branch;
use App\Models\Item;
use App\Models\StoredItem;
use App\User;
use Illuminate\Database\Seeder;

class StoredItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stored = new StoredItem();
        $stored->weight = 20;
        $stored->height = 1.5;
        $stored->length = 2;
        $stored->width = 2.3;
        $stored->count = 2;
        $stored->item_id = Item::first()->id;
        $stored->owner_id = User::first()->id;
        $stored->branch_id = Branch::first()->id;
        $stored->save();
    }
}

<?php

use App\Models\Branch;
use App\Models\Item;
use App\Models\Order;
use App\Models\StoredItem;
use App\Models\Users\Client;
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
        $employee = \App\Models\Role::where('name', 'employee')->first();

        $order = new Order();
        $order->ownerId = Client::first()->id;
        $order->totalCubage = 0;
        $order->totalWeight = 0;
        $order->totalPrice = 0;
        $order->totalDiscount = 0;
        $order->totalCount = 0;
        $order->branch = Branch::first()->id;
        $order->registeredBy = $employee->id;
        $order->save();



        for($i=0; $i<20; $i++){
            $stored = new StoredItem();
            $stored->weight = 20;
            $stored->height = 1.5;
            $stored->length = 2;
            $stored->width = 2.3;
            $stored->count = 2;
            $stored->item_id = Item::first()->id;
            $stored->ownerId = User::first()->id;
            $stored->order_id = User::first()->id;
            $stored->branch_id = Branch::first()->id;
            $stored->save();

            $order->totalCubage += $stored->height * $stored->weight * $stored->width;
            $order->totalWeight += $stored->weight;
            $order->totalPrice += 120;
            $order->totalDiscount += 10;
            $order->totalCount += $stored->count;
        }
        $order->save();
    }
}

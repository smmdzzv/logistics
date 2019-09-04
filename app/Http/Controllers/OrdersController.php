<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\StoredItem;
use App\Tariff;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function create(){
        $user = auth()->user();
        auth()->user()->branch;
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }

    public function store(StoreOrderRequest $request){
        $storedItems = $request->get('storedItems');

        foreach ($storedItems as $itemData){
            $stored = new StoredItem();
            $stored->width = $itemData['width'];
            $stored->height = $itemData['height'];
            $stored->length = $itemData['length'];
            $stored->weight = $itemData['weight'];
            $stored->count = $itemData['count'];
            return $stored->setPrice($itemData['tariffPricing']['id']);
            return $stored;
        }
    }
}

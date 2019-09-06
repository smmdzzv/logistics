<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Http\Requests\StoreOrderRequest;
use App\Order;
use App\StoredItem;
use App\Tariff;
use App\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

    }

    public function show(Order $order){
        $order->load(['storedItems.billingInfo', 'storedItems.item']);
        return view('orders.show', compact('order'));
    }

    public function create(){
        $user = auth()->user();
        auth()->user()->branch;
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }

    public function store(StoreOrderRequest $request){
        $storedItems = $request->get('storedItems');
        $clientId =  $request->get('clientId');
        $user = User::findOrFail($clientId);

        $order = new Order();
        $order->client_id = $clientId;
        $order->totalCubage = 0;
        $order->totalWeight = 0;
        $order->totalPrice = 0;
        $order->totalDiscount = 0;
        $order->totalCount = 0;

        auth()->user()->registeredOrders()->save($order);

        foreach ($storedItems as $itemData){
            $stored = new StoredItem();
            $stored->width = $itemData['width'];
            $stored->height = $itemData['height'];
            $stored->length = $itemData['length'];
            $stored->weight = $itemData['weight'];
            $stored->count = $itemData['count'];
            $stored->item_id = $itemData['item']['id'];
            $stored->branch_id = $itemData['branch']['id'];
            $stored->order_id = $order->id;

            $user->storedItems()->save($stored);

            $billing = $stored->getBillingInfo($itemData['tariffPricing']['id']);

            $billing->storedItem()->associate($stored);
            $billing->save();

            $order->totalCubage += $billing->totalCubage;
            $order->totalWeight += $billing->totalWeight;
            $order->totalPrice += $billing->totalPrice;
            $order->totalDiscount += $billing->totalDiscount;
            $order->totalCount += $itemData['count'];
        }

         $order->save();
        return $order;
    }

    public function update(StoreOrderRequest $request){

    }
}

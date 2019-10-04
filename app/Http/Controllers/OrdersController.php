<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\StoredItem;
use App\Models\Tariff;
use App\Models\Users\Client;
use App\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if(!auth()->user()->branch)
                abort(403, 'Для работы с заказами необходимо быть сотрудником филиала.');
            return $next($request);
        });
    }

    public function index(){
        $branches = Branch::all();
        return view('orders.index', compact('branches'));
    }

    public function show(Order $order){
        $order->load(['storedItems.billingInfo', 'storedItems.item', 'owner']);
        return view('orders.show', compact('order'));
    }

    public function create(){
        $user = auth()->user();
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }

    public function store(StoreOrderRequest $request){
        $storedItems = $request->get('storedItems');
        $clientId =  $request->get('clientId');
        $client = Client::findOrFail($clientId);

        $order = new Order();
        $order->ownerId = $clientId;
        $order->totalCubage = 0;
        $order->totalWeight = 0;
        $order->totalPrice = 0;
        $order->totalDiscount = 0;
        $order->totalCount = 0;
        $order->branch = auth()->user()->branch->id;

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

            $client->storedItems()->save($stored);

            $billing = $stored->getBillingInfo($itemData['tariffPricing']['id']);

            $billing->storedItem()->associate($stored);
            $billing->save();

            $order->totalCubage += $billing->totalCubage;
            $order->totalWeight += $billing->totalWeight;
            $order->totalPrice += $billing->totalPrice;
            $order->totalDiscount += $billing->totalDiscount;
            $order->totalCount += $itemData['count'];
        }

        $order->roundNumeric();
        $order->save();
        return $order;
    }

    public function update(StoreOrderRequest $request){

    }

    public function all(){
        return Order::with(['owner','registeredBy'])->paginate(10);
    }

    public function filteredByBranch(Branch $branch){
        if(isset($branch)){
            return $branch->orders()->with(['owner','registeredBy'])->paginate(10);
        }
        else abort(404, 'Филиал не найден');
    }

    public function filteredByUser(User $user){
        if(isset($user)){
            return $user->orders()->with(['owner','registeredBy'])->paginate(10);
        }
        else abort(404, 'Пользователь не найден');
    }
}

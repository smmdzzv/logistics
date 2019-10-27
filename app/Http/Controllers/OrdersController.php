<?php

namespace App\Http\Controllers;

use App\Data\RequestWriters\Order\OrderRequestWriter;
use App\Models\Branch;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Tariff;
use App\Models\Users\Client;
use App\User;
use stdClass;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('user.branch');

        $adminOnly = ['edit', 'update', 'destroy'];

        $this->middleware('roles.allow:admin')->only($adminOnly);
        $this->middleware('roles.deny:client,worker')->except($adminOnly);
    }

    public function index()
    {
        $branches = Branch::all();
        return view('orders.index', compact('branches'));
    }

    public function show(Order $order)
    {
        $order->load([
            'storedItemInfos',
            'storedItemInfos.billingInfo',
            'storedItemInfos.item',
            'storedItemInfos.storedItems',
            'owner'
        ]);
        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $user = auth()->user();
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }

    public function store(StoreOrderRequest $request)
    {
        //Create order
        $data = new stdClass();
        $data->client = Client::findOrFail($request->input('clientId'));
        $data->branch = Branch::findOrFail(auth()->user()->branch->id);
        $data->employee = auth()->user();

        foreach ($request->input('storedItemInfos') as $itemData) {
            $data->storedItemInfos[] = new StoredItemInfo([
                'width' => $itemData['width'],
                'height' => $itemData['height'],
                'length' => $itemData['length'],
                'weight' => $itemData['weight'],
                'count' => $itemData['count'],
                'item_id' => $itemData['item']['id'],
                'placeCount' => $itemData['placeCount'],
                'ownerId' => $data->client->id,
                'branch_id' => $data->branch->id
            ]);
        }

        $orderWriter = new OrderRequestWriter($data);
        $result = $orderWriter->write();

        return $result->order;
    }

    public function update(StoreOrderRequest $request)
    {

    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return Order::with(['owner', 'registeredBy'])->paginate($paginate);
    }

    public function activeOrders(Client $client){
        return $client->activeOrders;
    }

    public function filteredByBranch(Branch $branch)
    {
        $paginate = request()->paginate ?? 10;
        if (isset($branch)) {
            return $branch->orders()->with(['owner', 'registeredBy'])->paginate($paginate);
        } else abort(404, 'Филиал не найден');
    }

    public function filteredByUser(User $user)
    {
        $paginate = request()->paginate ?? 10;
        if (isset($user)) {
            return $user->orders()->with(['owner', 'registeredBy'])->paginate($paginate);
        } else abort(404, 'Пользователь не найден');
    }
}
